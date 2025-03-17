@extends('layouts.app')

@section('title', 'Item Management')

@section('content')
    <div class="container-fluid">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Item Management</h2>
                <p class="mt-2 text-sm text-gray-600">Manage store items</p>
            </div>
            <a href="{{ route('item.create') }}" 
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inventory Quantity</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if($items->isEmpty())
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No items found.</td>
                            </tr>
                        @else
                            @foreach($items as $index => $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->itemName }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->category->categoryName }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->inventory->sum('quantity') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('item.edit', $item->itemId) }}" 
                                           class="text-yellow-600 hover:text-yellow-900">
                                            <i class="lni lni-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="text-red-600 hover:text-red-900"
                                                @click="window.dispatchEvent(new CustomEvent('open-delete-modal', { 
                                                    detail: {
                                                        url: '{{ route('item.destroy', $item->itemId) }}'
                                                    }
                                                }))">
                                            <i class="lni lni-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            
            @if($items->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $items->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>

    @include('partials.modals.delete-modal')
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
