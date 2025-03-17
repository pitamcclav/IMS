@extends('layouts.app')

@section('title', 'Add New Category')

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Add New Category</h2>
            <p class="mt-2 text-sm text-gray-600">Create a new item category</p>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden p-6">
            <form action="{{ route('category.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label for="categoryName" class="block text-sm font-medium text-gray-700">Category Name</label>
                        <input type="text" 
                               id="categoryName" 
                               name="categoryName" 
                               placeholder="Enter category name"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               required>
                    </div>

                    <div>
                        <label for="isReturnable" class="block text-sm font-medium text-gray-700">Is Returnable</label>
                        <select id="isReturnable" 
                                name="isReturnable" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="lni lni-plus mr-2"></i>
                            Add Category
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
