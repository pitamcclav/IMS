@extends('layouts.app')

@section('title', 'Request Management')

@section('content')
    <div class="container-fluid" x-data="requestManager()">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Request Management</h2>
                <p class="mt-2 text-sm text-gray-600">View and manage inventory requests</p>
            </div>
            <a href="{{ route('requests.create') }}" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="lni lni-plus mr-2"></i>
                Add New Request
            </a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $index => $request)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->staff->staffName ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->store->storeName ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span @class([
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        'bg-yellow-100 text-yellow-800' => $request->status === 'pending',
                                        'bg-green-100 text-green-800' => $request->status === 'ready',
                                        'bg-blue-100 text-blue-800' => $request->status === 'picked'
                                    ])>
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                {{-- only get the date without the time --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button type="button" 
                                            class="text-blue-600 hover:text-blue-900"
                                            @click="$store.modals.open('view-request-{{ $request->requestId }}')">
                                        <i class="lni lni-eye"></i>
                                    </button>

                                    @if(auth()->user()->hasRole('staff'))
                                        @if($request->status == 'pending')
                                            <a href="{{ route('requests.edit', $request->requestId) }}" 
                                               class="text-yellow-600 hover:text-yellow-900">
                                                <i class="lni lni-pencil"></i>
                                            </a>
                                            <button type="button"
                                                    class="text-red-600 hover:text-red-900"
                                                    @click="window.dispatchEvent(new CustomEvent('open-delete-modal', { 
                                                        detail: {
                                                            id: '{{ $request->requestId }}', 
                                                            url: '{{ route('requests.destroy', $request->requestId) }}', 
                                                            redirect: window.location.href
                                                        }
                                                    }))">
                                                <i class="lni lni-trash-can"></i>
                                            </button>
                                        @endif
                                    @elseif(auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin'))
                                        <a href="{{ route('requests.edit', $request->requestId) }}" 
                                           class="text-yellow-600 hover:text-yellow-900">
                                            <i class="lni lni-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="text-red-600 hover:text-red-900"
                                                @click="window.dispatchEvent(new CustomEvent('open-delete-modal', { 
                                                    detail: {
                                                        id: '{{ $request->requestId }}', 
                                                        url: '{{ route('requests.destroy', $request->requestId) }}', 
                                                        redirect: window.location.href
                                                    }
                                                }))">
                                            <i class="lni lni-trash-can"></i>
                                        </button>
                                        @if($request->status == 'pending')
                                            <button type="button"
                                                    class="text-green-600 hover:text-green-900"
                                                    @click="updateStatus('{{ route('requests.updateStatus', $request->requestId) }}', 'ready')">
                                                <i class="lni lni-checkmark"></i>
                                            </button>
                                        @elseif($request->status == 'ready')
                                            <button type="button"
                                                    class="text-blue-600 hover:text-blue-900"
                                                    @click="updateStatus('{{ route('requests.updateStatus', $request->requestId) }}', 'picked')">
                                                <i class="lni lni-checkmark-circle"></i>
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>

                            <!-- View Request Modal -->
                            <div x-show="$store.modals.isOpen('view-request-{{ $request->requestId }}')"
                                 class="fixed inset-0 z-50 overflow-y-auto"
                                 style="display: none;">
                                <div class="flex items-center justify-center min-h-screen p-4">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                                         @click="$store.modals.close('view-request-{{ $request->requestId }}')"></div>

                                    <div class="relative bg-white rounded-lg max-w-lg w-full">
                                        <div class="px-4 py-5 sm:p-6">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Request Details</h3>
                                                    <div class="mt-4 space-y-3">
                                                        <p class="text-sm text-gray-500">
                                                            <span class="font-medium">Date:</span> {{ $request->date }}
                                                        </p>
                                                        <p class="text-sm text-gray-500">
                                                            <span class="font-medium">Status:</span> {{ ucfirst($request->status) }}
                                                        </p>
                                                        <p class="text-sm text-gray-500">
                                                            <span class="font-medium">Staff:</span> {{ $request->staff->staffName ?? 'N/A' }}
                                                        </p>
                                                        <p class="text-sm text-gray-500">
                                                            <span class="font-medium">Store:</span> {{ $request->store->storeName ?? 'N/A' }}
                                                        </p>
                                                        <div class="mt-4">
                                                            <h4 class="text-sm font-medium text-gray-900">Request Items</h4>
                                                            <ul class="mt-2 divide-y divide-gray-200">
                                                                @foreach($request->requestDetails as $detail)
                                                                    <li class="py-3">
                                                                        <p class="text-sm text-gray-600">
                                                                            <span class="font-medium">Item:</span> {{ $detail->item->itemName }}
                                                                        </p>
                                                                        <p class="text-sm text-gray-600">
                                                                            <span class="font-medium">Quantity:</span> {{ $detail->quantity }}
                                                                        </p>
                                                                        <p class="text-sm text-gray-600">
                                                                            <span class="font-medium">Color:</span> {{ $detail->colour->colourName }}
                                                                        </p>
                                                                        <p class="text-sm text-gray-600">
                                                                            <span class="font-medium">Size:</span> {{ $detail->size->sizeValue }}
                                                                        </p>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="button"
                                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                                                    @click="$store.modals.close('view-request-{{ $request->requestId }}')">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $requests->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        
        <!-- Include the delete modal once at the end of the page -->
        @include('partials.modals.delete-modal')
        
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </div>
@endsection
