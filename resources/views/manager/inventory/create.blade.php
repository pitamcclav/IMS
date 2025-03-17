@extends('layouts.app')

@section('title', 'Add New Inventory Item')

@section('content')
    <div class="container-fluid" 
         x-data="inventoryManager(@js($sizes), @js($colours))"
         x-init="init()">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Add New Inventory Item</h2>
            <p class="mt-2 text-sm text-gray-600">Create a new inventory item with variants</p>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" @submit="handleFormSubmit($event)">
                @csrf
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="itemid" class="block text-sm font-medium text-gray-700">Item</label>
                        <select name="itemid" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" 
                                id="itemid" required @change="handleSelectChange($event)">
                            <option value="" disabled selected>Select Item</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->itemId }}">{{ $item->itemName }}</option>
                            @endforeach
                            <option value="new">New Item</option>
                        </select>
                    </div>

                    <div>
                        <label for="supplierid" class="block text-sm font-medium text-gray-700">Supplier</label>
                        <select name="supplierid" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" 
                                id="supplierid" required @change="handleSelectChange($event)">
                            <option value="" disabled selected>Select Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->supplierId }}">{{ $supplier->supplierName }}</option>
                            @endforeach
                            <option value="new">New Supplier</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900">Item Variants</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Colour</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="variant-row">
                                    <td class="px-6 py-4">
                                        <select name="sizeIds[]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required @change="handleSelectChange($event)">
                                            <option value="" disabled selected>Select Size</option>
                                            @foreach($sizes as $size)
                                                <option value="{{ $size->sizeId }}">{{ $size->sizeValue }}</option>
                                            @endforeach
                                            <option value="new">New Size</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4">
                                        <select name="colourIds[]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required @change="handleSelectChange($event)">
                                            <option value="" disabled selected>Select Colour</option>
                                            @foreach($colours as $colour)
                                                <option value="{{ $colour->colourId }}">{{ $colour->colourName }}</option>
                                            @endforeach
                                            <option value="new">New Colour</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <button type="button" class="inline-flex items-center p-1 border border-transparent rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" @click="decrementQuantity($event)">
                                                <svg class="h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <input type="number" name="quantities[]" class="block w-20 px-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md text-center" value="1" min="1">
                                            <button type="button" class="inline-flex items-center p-1 border border-transparent rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" @click="incrementQuantity($event)">
                                                <svg class="h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button type="button" class="text-red-600 hover:text-red-900" @click="removeRow($event)">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" @click="addInventoryRow()">
                            <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Variant
                        </button>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Images</label>
                    <div class="mt-2">
                        <input 
                            type="file" 
                            name="image" 
                            id="fileInput"
                            accept="image/*"
                            multiple
                            data-allow-reorder="true"
                            data-max-file-size="3MB"
                            data-max-files="5"
                        >
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Upload up to 5 images (max 3MB each)</p>
                </div>

                <div class="mt-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Inventory Item
                    </button>
                </div>
            </form>
        </div>

        <!-- Modal Backdrop -->
        <div x-show="activeModal" 
        x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <!-- Include Modals -->
        @include('partials.modals.color-modal')
        @include('partials.modals.size-modal')
        @include('partials.modals.supplier-modal')
        @include('partials.modals.item-modal')
        
    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('alpine:init', () => {
                Alpine.data('inventoryManager', () => inventoryManager(@json($sizes), @json($colours)))
            })
        })
    </script>
@endpush
