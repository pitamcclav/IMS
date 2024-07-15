@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Requests Count -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Requests
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-primary">Total Requests: {{ $requestsCount }}</h5>
                        <p class="card-text">View detailed request information.</p>
                        <a href="{{ route('requests.index') }}" class="btn btn-primary">View Requests</a>
                    </div>
                </div>
            </div>

            <!-- Pending Requests Count -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        Pending Requests
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-warning">Pending Requests: {{ $pendingRequestsCount }}</h5>
                        <p class="card-text">View and manage pending requests.</p>
                        <a href="{{ route('requests.index') }}" class="btn btn-warning">View Pending Requests</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Requests Table -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Recent Requests
                    </div>
                    <div class="card-body">
                        @if($recentRequests->isEmpty())
                            <p>No recent requests found.</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($recentRequests as $request)
                                    <tr>
                                        <td>{{ $request->requestId }}</td>
                                        <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $request->status }}</td>
                                        <td><a href="{{ route('requests.show', $request->requestId) }}" class="btn btn-sm btn-info">View</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
