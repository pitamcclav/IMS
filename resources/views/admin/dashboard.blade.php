@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Users Count -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        Users
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-info">Total Users: {{ $usersCount }}</h5>
                        <p class="card-text">View detailed user information.</p>
                        <a href="{{ route('users.index') }}" class="btn btn-info">View Users</a>
                    </div>
                </div>
            </div>

            <!-- Stores Count -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        Stores
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-success">Total Stores: {{ $storesCount }}</h5>
                        <p class="card-text">View detailed store information.</p>
                        <a href="{{ route('stores') }}" class="btn btn-success">View Stores</a>
                    </div>
                </div>
            </div>

            <!-- Items Count -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        Items
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-warning">Total Items: {{ $itemsCount }}</h5>
                        <p class="card-text">View detailed item information.</p>
                        <a href="{{ route('item.index') }}" class="btn btn-warning">View Items</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Users Pie Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Users Distribution
                    </div>
                    <div class="card-body">
                        <canvas id="usersChart"></canvas>
                        <script type="application/json" id="usersData">@json($usersData)</script>
                    </div>
                </div>
            </div>

            <!-- Items Pie Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Items Distribution
                    </div>
                    <div class="card-body">
                        <canvas id="itemsChart"></canvas>
                        <script type="application/json" id="itemsData">@json($itemsData)</script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/admin_dashboard.js') }}"></script>
@endsection
