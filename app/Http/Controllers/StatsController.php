<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\BookingLimit;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StatsController extends Controller
{
    public function showDashboard(Request $request)
    {
        // Default to the current month if no specific month is selected
        $selectedMonth = $request->has('selected_month') ? Carbon::parse($request->selected_month) : Carbon::now();
    
        // Update the start and end dates based on the selected month
        $startDate = $selectedMonth->copy()->startOfMonth();
        $endDate = $selectedMonth->copy()->endOfMonth();
    
        // Fetch counts for different appointment statuses within the selected timeframe
        $statusCounts = $this->fetchStatusCounts($startDate, $endDate);
    
        // Fetch counts for different appointment types within the selected timeframe
        $typeCounts = $this->fetchTypeCounts($startDate, $endDate);
    
        // Fetch volume data by month within the selected timeframe
        $volumeData = $this->fetchVolumeDataByMonth($selectedMonth);
    
        // Format appointments data for JSON response
        $appointmentsData = [
            'labels' => ['Completed', 'No-show', 'Cancelled'],
            'datasets' => [
                [
                    'data' => [
                        $statusCounts['completedCount'],
                        $statusCounts['noShowCount'],
                        $statusCounts['cancelledCount']
                    ],
                    'backgroundColor' => ['#36a2eb', '#ff6384', '#cc65fe']
                ]
            ]
        ];
    
        // Define appointment type labels and data
        $appointmentTypeLabels = array_keys($typeCounts);
        $appointmentTypeData = array_values($typeCounts);
    
        // Return data as JSON response if it's an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'appointmentsData' => $appointmentsData,
                'appointmentTypeLabels' => $appointmentTypeLabels,
                'appointmentTypeData' => $appointmentTypeData,
                'volumeData' => $volumeData
            ]);
        }
    
        // Return the view with all data
        return view('medstaff', compact('appointmentTypeLabels', 'appointmentTypeData', 'volumeData', 'appointmentsData'));
    }
    


    protected function fetchStatusCounts($startDate, $endDate)
    {
        $completedCount = Appointment::join('slots', 'appointments.slot_id', '=', 'slots.id')
            ->join('booking_limits', 'slots.booking_limit_id', '=', 'booking_limits.id')
            ->where('appointments.status', 'completed')
            ->whereBetween('booking_limits.date', [$startDate, $endDate])
            ->count();
    
        $noShowCount = Appointment::join('slots', 'appointments.slot_id', '=', 'slots.id')
            ->join('booking_limits', 'slots.booking_limit_id', '=', 'booking_limits.id')
            ->where('appointments.status', 'no show')
            ->whereBetween('booking_limits.date', [$startDate, $endDate])
            ->count();
    
        $cancelledCount = Appointment::join('slots', 'appointments.slot_id', '=', 'slots.id')
            ->join('booking_limits', 'slots.booking_limit_id', '=', 'booking_limits.id')
            ->where('appointments.status', 'cancelled')
            ->whereBetween('booking_limits.date', [$startDate, $endDate])
            ->count();
    
        return compact('completedCount', 'noShowCount', 'cancelledCount');
    }
    
    protected function fetchTypeCounts($startDate, $endDate)
    {
        $appointments = Appointment::join('slots', 'appointments.slot_id', '=', 'slots.id')
            ->join('booking_limits', 'slots.booking_limit_id', '=', 'booking_limits.id')
            ->whereBetween('booking_limits.date', [$startDate, $endDate])
            ->get();
    
        return $this->countAppointmentsByType($appointments);
    }
    

    protected function fetchVolumeDataByMonth($selectedMonth)
    {
        $volumeLabelsByMonth = [];
        $volumeDataByMonth = [];
    
        $currentDate = $selectedMonth->copy()->startOfMonth();
        $endDate = $selectedMonth->copy()->endOfMonth();
    
        while ($currentDate <= $endDate) {
            $appointmentCount = Appointment::join('slots', 'appointments.slot_id', '=', 'slots.id')
                ->join('booking_limits', 'slots.booking_limit_id', '=', 'booking_limits.id')
                ->whereDate('booking_limits.date', $currentDate)
                ->count();
    
            $volumeLabelsByMonth[] = $currentDate->format('d M');
            $volumeDataByMonth[] = $appointmentCount;
    
            $currentDate->addDay();
        }
    
        return [
            'labels' => $volumeLabelsByMonth,
            'data' => $volumeDataByMonth,
        ];
    }
    


    public function generateReport(Request $request)
    {
        try {
            $timePeriod = 'monthly';
    
            // Get the selected year from the request or default to the current year
            $selectedYear = $request->input('year', date('Y'));
    
            // Retrieve distinct months for the selected year
            $distinctMonths = Appointment::selectRaw('DATE_FORMAT(booking_limits.date, "%Y-%m") as month')
                ->join('slots', 'appointments.slot_id', '=', 'slots.id')
                ->join('booking_limits', 'slots.booking_limit_id', '=', 'booking_limits.id')
                ->whereYear('booking_limits.date', $selectedYear)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('month');
    
            $appointmentCounts = [];
    
            foreach ($distinctMonths as $month) {
                $startOfMonth = Carbon::parse($month)->startOfMonth();
                $endOfMonth = Carbon::parse($month)->endOfMonth();
    
                $appointments = Appointment::select('appointments.*')
                    ->join('slots', 'appointments.slot_id', '=', 'slots.id')
                    ->join('booking_limits', 'slots.booking_limit_id', '=', 'booking_limits.id')
                    ->whereBetween('booking_limits.date', [$startOfMonth, $endOfMonth])
                    ->whereNotIn('appointments.status', ['reschedule'])
                    ->get();
    
                $appointmentStatusCounts = $this->countAppointmentsByStatus($appointments);
                $appointmentTypeCounts = $this->countAppointmentsByType($appointments);
    
                $appointmentCounts[$month] = [
                    'status' => $appointmentStatusCounts,
                    'types' => $appointmentTypeCounts,
                ];
            }
    
            $years = range(date('Y'), 2010); // Change the range as needed
    
            return view('layouts.Medstaff.reports', compact('appointmentCounts', 'timePeriod', 'selectedYear', 'years'));
        } catch (\Exception $e) {
            Log::error('Error generating report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while generating the report.');
        }
    }
    
    

    private function countAppointmentsByStatus($appointments)
    {
        $statusCounts = [
            'booked' => 0,
            'completed' => 0,
            'no show' => 0,
            'cancelled' => 0,
            'unknown' => 0, 
        ];

        foreach ($appointments as $appointment) {
            $status = $appointment->status ?? 'unknown'; 
            $statusCounts[$status]++;
        }

        return $statusCounts;
    }

    private function countAppointmentsByType($appointments)
    {
        $typeCounts = [
            'consultation' => 0,
            'checkup' => 0,
            'follow-up' => 0,
            'vaccination' => 0,
            'urgent' => 0,
            'medcert' => 0,
            'unknown' => 0,
        ];

        foreach ($appointments as $appointment) {
            $type = $appointment->type ?? 'unknown'; 
            $typeCounts[$type]++;
        }

        return $typeCounts;
    }

}
