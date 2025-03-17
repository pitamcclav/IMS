@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Create Email Template</h1>
        </div>

        <div class="max-w-2xl">
            <form action="{{ route('emailTemplates.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Template Name</label>
                                <div class="mt-1">
                                    <input type="text" 
                                        id="name" 
                                        name="name" 
                                        required
                                        placeholder="Enter template name"
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700">Template Subject</label>
                                <div class="mt-1">
                                    <input type="text" 
                                        id="subject" 
                                        name="subject" 
                                        required
                                        placeholder="Enter email subject"
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="store" class="block text-sm font-medium text-gray-700">Store</label>
                                <div class="mt-1">
                                    <select id="store" 
                                        name="store" 
                                        required
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="" disabled selected>Select store</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->storeId }}">{{ $store->storeName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Template Type</label>
                                <div class="mt-1">
                                    <select id="type" 
                                        name="type" 
                                        required
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="" disabled selected>Select type</option>
                                        <option value="request_created">Request Created</option>
                                        <option value="status_changed">Status Changed</option>
                                        <option value="low_stock">Low Stock</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="body" class="block text-sm font-medium text-gray-700">Template Content</label>
                                <div class="mt-1">
                                    <textarea id="body" 
                                        name="body" 
                                        rows="10" 
                                        required
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        placeholder="Enter email template content"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Template
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
