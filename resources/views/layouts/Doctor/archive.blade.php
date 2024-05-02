@extends('layouts.Doctor.navbar')

@section('content')
<div class="container">
    <div class="table-responsive">
        <table id="myData" class="table table-bordered table-striped">
            <!-- Table Headers -->
            <thead>
                <tr>
                    <th colspan="4">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-6">
                                <h1 class="h2">Archives</h1>
                            </div>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Deleted At</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($softDeletedPatients as $index => $patient)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $patient->first_name }} {{ $patient->middle_name }} {{ $patient->last_name }}</td>
                    <td class="text-center">{{ $patient->deleted_at->format('F d, Y') }}</td>
                    <td class="text-center">
                        <form action="{{ route('patientsRecord.restore', $patient->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success">Restore</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
