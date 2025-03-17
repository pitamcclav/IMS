@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container-fluid" x-data="adminDashboard()" x-init="init" @resize.window="init">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Admin Dashboard</h2>
            <p class="mt-2 text-sm text-gray-600">Overview of system statistics and performance</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Users Stats -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Users</h3>
                </div>
                <div class="p-4">
                    <div class="flex items-baseline">
                        <h5 class="text-2xl font-bold text-gray-800">{{ $usersCount }}</h5>
                        <p class="ml-2 text-sm text-gray-600">total users</p>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">{{ $usersData['activeUsers'] }} users currently active</p>
                    <div class="mt-4">
                        <a href="{{ route('users.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="lni lni-users mr-2"></i>
                            Manage Users
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stores Stats -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Stores</h3>
                </div>
                <div class="p-4">
                    <div class="flex items-baseline">
                        <h5 class="text-2xl font-bold text-gray-800">{{ $storesCount }}</h5>
                        <p class="ml-2 text-sm text-gray-600">total stores</p>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">Manage store locations and inventory</p>
                    <div class="mt-4">
                        <a href="{{ route('stores') }}" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="lni lni-apartment mr-2"></i>
                            Manage Stores
                        </a>
                    </div>
                </div>
            </div>

            <!-- Items Stats -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Items</h3>
                </div>
                <div class="p-4">
                    <div class="flex items-baseline">
                        <h5 class="text-2xl font-bold text-gray-800">{{ $itemsCount }}</h5>
                        <p class="ml-2 text-sm text-gray-600">total items</p>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">Manage inventory items</p>
                    <div class="mt-4">
                        <a href="{{ route('item.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="lni lni-grid mr-2"></i>
                            Manage Items
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <!-- Users Distribution Chart -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Users Distribution</h3>
                </div>
                <div class="p-4">
                    <div class="h-[300px] relative">
                        <canvas id="usersChart"></canvas>
                    </div>
                    <script type="application/json" id="usersData">@json($usersData)</script>
                </div>
            </div>

            <!-- Items Distribution Chart -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Items Distribution</h3>
                </div>
                <div class="p-4">
                    <div class="h-[300px] relative">
                        <canvas id="itemsChart"></canvas>
                    </div>
                    <script type="application/json" id="itemsData">@json($itemsData)</script>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
@endsection
