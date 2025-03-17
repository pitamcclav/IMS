@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
    <div class="container-fluid">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Inventory Management</h2>
                <p class="mt-2 text-sm text-gray-600">Track and manage inventory items</p>
            </div>
            <a href="{{ route('inventory.create') }}" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="lni lni-plus mr-2"></i>
                Add New Item 
            </a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Initial Quantity</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if ($inventories->isEmpty())
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No inventory items found.</td>
                            </tr>
                        @else
                            @foreach ($inventories as $index => $inventory)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $inventory->item->itemName ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $inventory->colour->colourName ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $inventory->size->sizeValue ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $inventory->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $inventory->initialQuantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('inventory.edit', $inventory->inventoryId) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="lni lni-pencil"></i>
                                        </a>
                                        <button class="text-red-600 hover:text-red-900" 
                                                @click.prevent="$dispatch('open-delete-modal', {url: '{{ route('inventory.destroy', $inventory->inventoryId) }}'})">
                                            <i class="lni lni-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-200">
                {{ $inventories->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            document.addEventListener('open-delete-modal', (e) => {
                const deleteModal = Alpine.evaluate(document.querySelector('[x-data="deleteModal()"]'), 'openModal');
                deleteModal(e.detail.url);
            });
        });
    </script>
@endsection
