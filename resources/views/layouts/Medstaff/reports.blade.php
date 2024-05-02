@extends('layouts.Medstaff.navbar')

@section('content')
<div class="container">
    <div class="table-responsive">
        <table id="myDataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th colspan="11">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-6">
                                <h1 class="h2">Summary of Reports</h1>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="year">Select Year:</label>
                                <select class="form-control" id="yearSelect">
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="#" class="btn btn-primary" id="printButton">Print</a>
                            </div>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th>{{ ucfirst($selectedYear) }}</th>
                    <th colspan="4">Appointment Status</th>
                    <th colspan="6">Appointment Type</th>
                </tr>
                <tr>
                    <th>Date</th>
                    <th>Booked</th>
                    <th>Completed</th>
                    <th>No Show</th>
                    <th>Cancelled</th>
                    <th>General Consultation</th>
                    <th>Follow-up Visit</th>
                    <th>Check-up</th>
                    <th>Urgent Care</th>
                    <th>Tetanus Vaccination</th>
                    <th>Medical Certification</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($appointmentCounts as $timePeriod => $counts)
                    @php
                        // Splitting the period into month and year
                        [$year, $month] = explode('-', $timePeriod);
                        // Creating a Carbon instance with the year and month
                        $date = Carbon\Carbon::createFromDate($year, $month, 1);
                        // Formatting the date as "Month Year"
                        $formattedDate = $date->format('F Y');
                    @endphp
                    @if ($year == $selectedYear) <!-- Check if the year matches the selected year -->
                        <tr>
                            <td>{{ $formattedDate }}</td>
                            <td>{{ $counts['status']['booked'] ?? 0 }}</td>
                            <td>{{ $counts['status']['completed'] ?? 0 }}</td>
                            <td>{{ $counts['status']['no show'] ?? 0 }}</td>
                            <td>{{ $counts['status']['cancelled'] ?? 0 }}</td>
                            <td>{{ $counts['types']['consultation'] ?? 0 }}</td>
                            <td>{{ $counts['types']['follow-up'] ?? 0 }}</td>
                            <td>{{ $counts['types']['checkup'] ?? 0 }}</td>
                            <td>{{ $counts['types']['urgent'] ?? 0 }}</td>
                            <td>{{ $counts['types']['vaccination'] ?? 0 }}</td>
                            <td>{{ $counts['types']['medcert'] ?? 0 }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('printButton').addEventListener('click', function() {
        var printContents = document.getElementById('myDataTable').outerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    });

    // JavaScript to handle year change
    document.getElementById('yearSelect').addEventListener('change', function() {
        var selectedYear = this.value;
        window.location.href = '{{ route("generate.report") }}?year=' + selectedYear;
    });
</script>
@endsection
