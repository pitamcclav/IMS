@extends('layouts.app')

@section('title', 'Create New Request')

@section('content')
    <div class="container-fluid" 
         x-data="requestManager()"
         x-init="$watch('loading', value => {
            if (value) {
                document.body.style.cursor = 'wait';
            } else {
                document.body.style.cursor = 'default';
            }
         })">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Create New Request</h2>
            <p class="mt-2 text-sm text-gray-600">Submit a new inventory request</p>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <form @submit.prevent="submitRequest" class="p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="storeId" class="block text-sm font-medium text-gray-700">Store</label>
                        <select id="store" 
                                x-model="storeId"
                                @change="onStoreChange($event.target.value)"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                required>
                            <option value="">Select Store</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->storeId }}" 
                                        data-manager-id="{{ $store->managerId }}">
                                    {{ $store->storeName }}
                                </option>
                            @endforeach
                        </select>
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
                                <option value="{{ $staff->staffId }}">{{ $staff->staffName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900">Request Items</h3>
                    <div class="mt-4">
                        <template x-for="(detail, detailIndex) in requestDetails" :key="detailIndex">
                            <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-sm font-medium text-gray-700">Item #<span x-text="detailIndex + 1"></span></h4>
                                    <button type="button" @click="removeRequestDetail(detailIndex)"
                                            class="text-red-600 hover:text-red-900">
                                        <i class="lni lni-close"></i>
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Item</label>
                                        <select x-model="detail.itemId"
                                                @change="onItemChange($event.target.value, detailIndex)"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                                required>
                                            <option value="">Select Item</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->itemId }}">{{ $item->itemName }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <template x-for="(variant, variantIndex) in detail.variants" :key="variantIndex">
                                        <div class="grid grid-cols-4 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Color</label>
                                                <select x-model="variant.colourId"
                                                        @change="onColourChange($event.target.value, detailIndex, variantIndex)"
                                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                                        required>
                                                    <option value="">Select Color</option>
                                                    <template x-for="colour in colours[detail.itemId] || []">
                                                        <option :value="colour.colourId" x-text="colour.colourName"></option>
                                                    </template>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Size</label>
                                                <select x-model="variant.sizeId"
                                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                                        required>
                                                    <option value="">Select Size</option>
                                                    <template x-if="sizes[detail.itemId] && sizes[detail.itemId][variant.colourId]">
                                                        <template x-for="size in sizes[detail.itemId][variant.colourId]">
                                                            <option :value="size.sizeId" x-text="size.sizeValue"></option>
                                                        </template>
                                                    </template>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                                <div class="mt-1 flex items-center space-x-2">
                                                    <button type="button" @click="decrementQuantity(detailIndex, variantIndex)"
                                                            class="inline-flex items-center p-1 border border-transparent rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                        <i class="lni lni-minus"></i>
                                                    </button>
                                                    <input type="number"
                                                           x-model.number="variant.quantity"
                                                           class="block w-20 px-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md text-center"
                                                           min="1"
                                                           required>
                                                    <button type="button" @click="incrementQuantity(detailIndex, variantIndex)"
                                                            class="inline-flex items-center p-1 border border-transparent rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                        <i class="lni lni-plus"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="flex items-end">
                                                <button type="button" @click="removeVariant(detailIndex, variantIndex)"
                                                        class="text-red-600 hover:text-red-900"
                                                        :disabled="detail.variants.length === 1">
                                                    <i class="lni lni-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    <button type="button" @click="addVariant(detailIndex)"
                                            class="mt-2 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="lni lni-plus mr-1"></i>
                                        Add Variant
                                    </button>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addRequestDetail"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="lni lni-plus mr-2"></i>
                            Add Item
                        </button>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit"
                            :disabled="loading"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <template x-if="loading">
                            <i class="lni lni-spinner-arrow animate-spin mr-2"></i>
                        </template>
                        <template x-if="!loading">
                            <i class="lni lni-save mr-2"></i>
                        </template>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="{{ Auth::guard('staff')->user()->roles->first()->name }}">
    <meta name="user-id" content="{{ Auth::guard('staff')->user()->staffId }}">
@endsection
