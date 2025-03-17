@extends('layouts.app')

@section('title', 'Manager Dashboard')

@section('content')
    <div class="container-fluid" x-data="managerDashboard()" x-init="init">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Manager Dashboard</h2>
            <p class="mt-2 text-sm text-gray-600">Monitor inventory and request management</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                    <p class="mt-1 text-sm text-gray-600">View and manage store items</p>
                    <div class="mt-4">
                        <a href="{{ route('item.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="lni lni-grid mr-2"></i>
                            Manage Items
                        </a>
                    </div>
                </div>
            </div>

            <!-- Inventory Stats -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Inventory</h3>
                </div>
                <div class="p-4">
                    <div class="flex items-baseline">
                        <h5 class="text-2xl font-bold text-gray-800">{{ $inventoryItemsCount }}</h5>
                        <p class="ml-2 text-sm text-gray-600">total inventory</p>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">Monitor stock levels</p>
                    <div class="mt-4">
                        <a href="{{ route('inventory.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="lni lni-list mr-2"></i>
                            View Inventory
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Pending Requests</h3>
                </div>
                <div class="p-4">
                    <div class="flex items-baseline">
                        <h5 class="text-2xl font-bold text-gray-800">{{ $pendingRequestsCount }}</h5>
                        <p class="ml-2 text-sm text-gray-600">pending</p>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">Handle pending inventory requests</p>
                    <div class="mt-4">
                        <a href="{{ route('requests.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="lni lni-envelope mr-2"></i>
                            View Requests
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <!-- Inventory Distribution Chart -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Inventory Distribution</h3>
                </div>
                <div class="p-4">
                    <div class="h-[300px] relative">
                        <canvas id="inventoryChart"></canvas>
                    </div>
                    <script type="application/json" id="inventoryData">@json($inventoryData)</script>
                </div>
            </div>

            <!-- Requests Distribution Chart -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Requests Overview</h3>
                </div>
                <div class="p-4">
                    <div class="h-[300px] relative">
                        <canvas id="requestsChart"></canvas>
                    </div>
                    <script type="application/json" id="pendingRequestsCount">{{ $pendingRequestsCount }}</script>
                </div>
            </div>
        </div>
    </div>
@endsection
