@extends('layouts.Medstaff.navbar')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <div class="table-responsive">
        <table id="myDataTable" class="table table-bordered table-striped">
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
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $patient->first_name }} {{ $patient->middle_name }} {{ $patient->last_name }}</td>
                    <td>{{ $patient->deleted_at->format('F d, Y') }}</td>
                    <td class="text-center">
                        <form action="{{ route('patients.restore', $patient->id) }}" method="POST">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
@if(Session::has('success'))
<script>
    swal("Restored successfully!","{!! Session::get('success') !!}","success",{
        button: "OK",
    });
</script>
@endif
@endsection
