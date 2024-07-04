@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Key Metrics -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        Items
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-success">Total Items: {{ $itemsCount }}</h5>
                        <p class="card-text">View detailed items.</p>
                        <a href="{{ route('item.index') }}" class="btn btn-success">View Items</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Inventory Levels
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Total Items: {{ $inventoryItemsCount }}</h5>
                        <p class="card-text">View detailed inventory levels.</p>
                        <a href="{{ route('inventory.index') }}" class="btn btn-primary">View Inventory</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        Pending Requests
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Pending: {{ $pendingRequestsCount }}</h5>
                        <p class="card-text">View and manage pending requests.</p>
                        <a href="{{ route('requests.index') }}" class="btn btn-warning">View Requests</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Pie Chart for Inventory -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Inventory Items Distribution
                    </div>
                    <div class="card-body">
                        <canvas id="inventoryChart"></canvas>
                        <script type="application/json" id="inventoryData">@json($inventoryData)</script>
                    </div>
                </div>
            </div>

            <!-- Pie Chart for Pending Requests -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Pending Requests
                    </div>
                    <div class="card-body">
                        <canvas id="requestsChart"></canvas>
                        <script type="application/json" id="pendingRequestsCount">{{ $pendingRequestsCount }}</script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection
