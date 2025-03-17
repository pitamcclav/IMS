@extends('layouts.app')

@section('title', 'Edit Inventory Item')

@section('content')
    <div class="container-fluid" x-data="{
        quantity: {{ $inventory->quantity }},
        initialQuantity: {{ $inventory->initialQuantity }},
        validateQuantity() {
            const qty = parseInt(this.quantity);
            if (isNaN(qty) || qty < 0) {
                return 'Quantity must be a positive number';
            }
            return '';
        }
    }">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Edit Inventory Item</h2>
            <p class="mt-2 text-sm text-gray-600">Update inventory item details</p>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <form action="{{ route('inventory.update', $inventory->inventoryId) }}" method="POST" class="p-6 space-y-6" @submit="validateQuantity() !== ''">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="itemid" class="block text-sm font-medium text-gray-700">Item</label>
                        <select name="itemid" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" 
                                id="itemid" required>
                            @foreach ($items as $item)
                                <option value="{{ $item->itemId }}" {{ $inventory->itemId == $item->itemId ? 'selected' : '' }}>
                                    {{ $item->itemName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="colourid" class="block text-sm font-medium text-gray-700">Color</label>
                        <select name="colourid" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" 
                                id="colourid" required>
                            @foreach ($colors as $color)
                                <option value="{{ $color->colourId }}" {{ $inventory->colourId == $color->colourId ? 'selected' : '' }}>
                                    {{ $color->colourName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="sizeid" class="block text-sm font-medium text-gray-700">Size</label>
                        <select name="sizeid" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" 
                                id="sizeid" required>
                            @foreach ($sizes as $size)
                                <option value="{{ $size->sizeId }}" {{ $inventory->sizeId == $size->sizeId ? 'selected' : '' }}>
                                    {{ $size->sizeValue }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Current Quantity</label>
                        <div class="mt-1 flex items-center space-x-2">
                            <button type="button" class="inline-flex items-center p-1 border border-transparent rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" 
                                    @click="quantity > 0 ? quantity-- : quantity">
                                <svg class="h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <input type="number" name="quantity" id="quantity" 
                                   class="block w-20 px-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md text-center"
                                   x-model.number="quantity" min="0" required>
                            <button type="button" class="inline-flex items-center p-1 border border-transparent rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" 
                                    @click="quantity++">
                                <svg class="h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-1 text-sm text-red-600" x-show="validateQuantity() !== ''" x-text="validateQuantity()"></div>
                    </div>

                    <div>
                        <label for="initialQuantity" class="block text-sm font-medium text-gray-700">Initial Quantity</label>
                        <div class="mt-1 flex items-center space-x-2">
                            <button type="button" class="inline-flex items-center p-1 border border-transparent rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" 
                                    @click="initialQuantity > 0 ? initialQuantity-- : initialQuantity">
                                <svg class="h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <input type="number" name="initialQuantity" id="initialQuantity" 
                                   class="block w-20 px-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md text-center"
                                   x-model.number="initialQuantity" min="0" required>
                            <button type="button" class="inline-flex items-center p-1 border border-transparent rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" 
                                    @click="initialQuantity++">
                                <svg class="h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="lni lni-save mr-2"></i>
                        Update Inventory Item
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
