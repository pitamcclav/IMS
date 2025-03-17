@extends('layouts.app')

@section('title', 'Edit Request')

@php
    // Transform the request details into the required data structure with the correct relationship names
    $requestData = $request->requestDetails->groupBy('itemId')->map(function($details) {
        $firstDetail = $details->first();
        return [
            'itemId' => strval($firstDetail->itemId),
            'itemName' => $firstDetail->item->itemName,
            'variants' => $details->map(function($detail) {
                return [
                    'colourId' => strval($detail->colourId),
                    'sizeId' => strval($detail->sizeId),
                    'quantity' => $detail->quantity
                ];
            })->toArray()
        ];
    })->values()->toArray();
    
@endphp

@section('content')
    <script>
        window.requestData = @json($requestData);
    </script>

    <div class="container-fluid" 
         x-data="(() => {
             const manager = requestManager();
             manager.storeId = '{{ $request->storeId }}';
             manager.staffId = '{{ $request->staffId }}';
             manager.requestDetails = window.requestData;
             return manager;
         })()"
         x-init="(() => {
             $nextTick(async () => {
                 if (storeId) {
                 console.log(storeId);
                     await loadInitialData();
                     for (const [index, detail] of requestDetails.entries()) {
                         if (detail.itemId) {
                         console.log(detail.itemId);
                             await onItemChange(detail.itemId, index);
                             for (const variant of detail.variants) {
                                 if (variant.colourId) {
                                 co
                                     await fetchSizesForColour(detail.itemId, variant.colourId);
                                 }
                             }
                         }
                     }
                 }
             });
         })()">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Edit Request</h2>
            <p class="mt-2 text-sm text-gray-600">Update request details</p>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <form action="{{ route('requests.update', $request->requestId) }}" 
                  method="POST" 
                  @submit.prevent="submitRequest"
                  class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="storeId" class="block text-sm font-medium text-gray-700">Store</label>
                        @if(auth()->user()->hasRole('manager'))
                            <input type="hidden" name="storeId" x-model="storeId" value="{{ $request->store->storeId }}">
                            <input type="text" 
                                   value="{{ $request->store->storeName }}" 
                                   class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 bg-gray-100 sm:text-sm rounded-md" 
                                   disabled>
                        @else
                            <select id="store" 
                                    x-model="storeId"
                                    @change="onStoreChange($event.target.value)"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                    required>
                                <option value="">Select Store</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->storeId }}" 
                                            data-manager-id="{{ $store->managerId }}"
                                            {{ $request->storeId == $store->storeId ? 'selected' : '' }}>
                                        {{ $store->storeName }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div>
                        <label for="staffId" class="block text-sm font-medium text-gray-700">Staff</label>
                        <select id="staff" 
                                x-model="staffId"
                                :disabled="isStaffSelectDisabled"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                required>
                            <option value="">Select Staff</option>
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->staffId }}" 
                                        {{ $request->staffId == $staff->staffId ? 'selected' : '' }}>
                                    {{ $staff->staffName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin'))
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" 
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                    required>
                                <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="ready" {{ $request->status == 'ready' ? 'selected' : '' }}>Ready</option>
                                <option value="picked" {{ $request->status == 'picked' ? 'selected' : '' }}>Picked</option>
                            </select>
                        </div>
                    @endif
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900">Request Items</h3>
                    <div class="mt-4">
                        <template x-for="(detail, detailIndex) in requestDetails" :key="detailIndex">
                            <div class="mb-6 p-4 border border-gray-200 rounded-lg" :class="{'opacity-75': '{{ $request->status }}' === 'picked'}">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-sm font-medium text-gray-700">Item #<span x-text="detailIndex + 1"></span></h4>
                                    <button type="button" 
                                            @click="removeRequestDetail(detailIndex)"
                                            :disabled="'{{ $request->status }}' !== 'pending'"
                                            class="text-red-600 hover:text-red-900 disabled:opacity-50">
                                        <i class="lni lni-close"></i>
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Item</label>
                                        <select x-model="detail.itemId"
                                                @change="onItemChange($event.target.value, detailIndex)"
                                                :disabled="'{{ $request->status }}' !== 'pending'"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                                required>
                                            <option value="">Select Item</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->itemId }}" 
                                                        :selected="detail.itemId == '{{ $item->itemId }}'">
                                                    {{ $item->itemName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <template x-for="(variant, variantIndex) in detail.variants" :key="variantIndex">
                                        <div class="grid grid-cols-4 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Color</label>
                                                <select x-model="variant.colourId"
                                                        @change="onColourChange($event.target.value, detailIndex, variantIndex)"
                                                        :disabled="!detail.itemId || '{{ $request->status }}' !== 'pending'"
                                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                                        required>
                                                    <option value="">Select Color</option>
                                                    <template x-for="colour in colours[detail.itemId] || []">
                                                        <option :value="colour.colourId" 
                                                                :selected="variant.colourId == colour.colourId"
                                                                x-text="colour.colourName"></option>
                                                    </template>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Size</label>
                                                <select x-model="variant.sizeId"
                                                        :disabled="!variant.colourId || '{{ $request->status }}' !== 'pending'"
                                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                                        required>
                                                    <option value="">Select Size</option>
                                                    <template x-if="sizes[detail.itemId] && sizes[detail.itemId][variant.colourId]">
                                                        <template x-for="size in sizes[detail.itemId][variant.colourId]">
                                                            <option :value="size.sizeId" 
                                                                    :selected="variant.sizeId == size.sizeId"
                                                                    x-text="size.sizeValue"></option>
                                                        </template>
                                                    </template>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                                <div class="mt-1 flex items-center space-x-2">
                                                    <button type="button" 
                                                            @click="decrementQuantity(detailIndex, variantIndex)"
                                                            :disabled="'{{ $request->status }}' !== 'pending'"
                                                            class="inline-flex items-center p-1 border border-transparent rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-50">
                                                        <i class="lni lni-minus"></i>
                                                    </button>
                                                    <input type="number"
                                                           x-model.number="variant.quantity"
                                                           :disabled="'{{ $request->status }}' !== 'pending'"
                                                           class="block w-20 px-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md text-center"
                                                           min="1"
                                                           required>
                                                    <button type="button" 
                                                            @click="incrementQuantity(detailIndex, variantIndex)"
                                                            :disabled="'{{ $request->status }}' !== 'pending'"
                                                            class="inline-flex items-center p-1 border border-transparent rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-50">
                                                        <i class="lni lni-plus"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="flex items-end">
                                                <button type="button" 
                                                        @click="removeVariant(detailIndex, variantIndex)"
                                                        :disabled="detail.variants.length === 1 || '{{ $request->status }}' !== 'pending'"
                                                        class="text-red-600 hover:text-red-900 disabled:opacity-50">
                                                    <i class="lni lni-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    <button type="button" 
                                            @click="addVariant(detailIndex)"
                                            x-show="'{{ $request->status }}' === 'pending'"
                                            class="mt-2 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="lni lni-plus mr-1"></i>
                                        Add Variant
                                    </button>
                                </div>
                            </div>
                        </template>

                        <button type="button" 
                                @click="addRequestDetail"
                                x-show="'{{ $request->status }}' === 'pending'"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="lni lni-plus mr-2"></i>
                            Add Item
                        </button>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit"
                            :disabled="loading || '{{ $request->status }}' !== 'pending'"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                        <template x-if="loading">
                            <i class="lni lni-spinner-arrow animate-spin mr-2"></i>
                        </template>
                        <template x-if="!loading">
                            <i class="lni lni-save mr-2"></i>
                        </template>
                        Update Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="{{ Auth::guard('staff')->user()->roles->first()->name }}">
    <meta name="user-id" content="{{ Auth::guard('staff')->user()->staffId }}">
@endsection