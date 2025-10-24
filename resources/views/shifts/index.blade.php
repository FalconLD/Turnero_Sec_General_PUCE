@extends('adminlte::page')

@section('title', 'Shift Management')

@section('content_header')
    <h1 class="text-center mb-4">Shifts</h1>
@stop

@section('content')
<div class="container">
    <div class="card shadow-lg">
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cubicle</th>
                        <th>Date</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shifts as $shift)
                        <tr>
                            <td>{{ $shift->id }}</td>
                            <td>{{ $shift->cubicle->name ?? 'N/A' }}</td>
                            <td>{{ $shift->date }}</td>
                            <td>{{ $shift->start_time }}</td>
                            <td>{{ $shift->end_time }}</td>
                            <td>{{ $shift->status == 1 ? 'Active' : 'Inactive' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
