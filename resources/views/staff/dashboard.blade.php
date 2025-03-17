@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Staff Dashboard</h2>
            <p class="mt-2 text-sm text-gray-600">Track your requests and inventory status</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Total Requests -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Total Requests</h3>
                </div>
                <div class="p-4">
                    <div class="flex items-baseline">
                        <h5 class="text-2xl font-bold text-gray-800">{{ $requestsCount }}</h5>
                        <p class="ml-2 text-sm text-gray-600">requests</p>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">View all your inventory requests</p>
                    <div class="mt-4">
                        <a href="{{ route('requests.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="lni lni-list mr-2"></i>
                            View Requests
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
                    <p class="mt-1 text-sm text-gray-600">View and manage pending requests</p>
                    <div class="mt-4">
                        <a href="{{ route('requests.index') }}?status=pending" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="lni lni-timer mr-2"></i>
                            View Pending
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Requests -->
        <div class="mt-6">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Recent Requests</h3>
                </div>
                <div class="p-4">
                    @if($recentRequests->isEmpty())
                        <p class="text-gray-600">No recent requests found.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request ID</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentRequests as $request)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                #{{ $request->requestId }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $request->created_at->format('Y-m-d') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ''}}
                                                    {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' : ''}}
                                                    {{ $request->status === 'rejected' ? 'bg-red-100 text-red-800' : ''}}
                                                    {{ $request->status === 'ready' ? 'bg-blue-100 text-blue-800' : ''}}
                                                    {{ $request->status === 'picked' ? 'bg-purple-100 text-purple-800' : ''}}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('requests.show', $request->requestId) }}" 
                                                    class="text-blue-600 hover:text-blue-900">
                                                    <i class="lni lni-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
