@extends('layouts.app')

@section('title', 'Generate Report')

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Generate Reports</h2>
            <p class="mt-2 text-sm text-gray-600">Generate custom reports based on various parameters</p>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden p-6">
            <form action="{{ route('report.create') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="dateRange" class="block text-sm font-medium text-gray-700">Date Range</label>
                        <input type="text" id="dateRange" name="dateRange" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm daterangepicker"
                               required>
                    </div>

                    <div>
                        <label for="store" class="block text-sm font-medium text-gray-700">Store</label>
                        <select id="store" name="storeId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            @foreach($stores as $store)
                                <option value="{{ $store->storeId }}">{{ $store->storeName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="item" class="block text-sm font-medium text-gray-700">Item</label>
                        <select id="item" name="itemId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            @foreach($items as $item)
                                <option value="{{ $item->itemId }}">{{ $item->itemName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="category" name="categoryId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            @foreach($categories as $category)
                                <option value="{{ $category->categoryId }}">{{ $category->categoryName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="staff" class="block text-sm font-medium text-gray-700">Staff</label>
                        <select id="staff" name="staffId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->staffId }}">{{ $staff->staffName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="lni lni-graph mr-2"></i>
                        Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
