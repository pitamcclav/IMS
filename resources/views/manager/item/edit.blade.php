@extends('layouts.app')

@section('title', 'Edit Item')

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Edit Item</h2>
            <p class="mt-2 text-sm text-gray-600">Update item details</p>
        </div>
        
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <form action="{{ route('item.update', $item->itemId) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="itemName" class="block text-sm font-medium text-gray-700">Item Name</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="lni lni-package text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   id="itemName" 
                                   name="itemName" 
                                   value="{{ $item->itemName }}"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="lni lni-grid-alt text-gray-400"></i>
                            </div>
                            <select id="category" 
                                    name="categoryId" 
                                    class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->categoryId }}" 
                                            {{ $item->categoryId == $category->categoryId ? 'selected' : '' }}>
                                        {{ $category->categoryName }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="lni lni-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute top-3 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="lni lni-text-align-justify text-gray-400"></i>
                        </div>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                  required>{{ $item->description }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('item.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="lni lni-close mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="lni lni-save mr-2"></i>
                        Update Item
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
