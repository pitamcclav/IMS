@extends('layouts.app')
@section('title', 'Edit Store')
@section('content')
    <div class="container">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Edit Store</h1>
        </div>

        <div class="max-w-2xl">
            <form action="{{ route('stores.update', $store->storeId) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="storeName" class="block text-sm font-medium text-gray-700">Store Name</label>
                                <div class="mt-1">
                                    <input type="text" 
                                        id="storeId" 
                                        name="storeId" 
                                        value="{{ $store->storeId }}"
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm hidden" >
                                    <input type="text" 
                                        id="storeName" 
                                        name="storeName" 
                                        value="{{ $store->storeName }}"
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                                <div class="mt-1">
                                    <input type="text" 
                                        id="location" 
                                        name="location" 
                                        value="{{ $store->location }}"
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="staffId" class="block text-sm font-medium text-gray-700">Staff In-charge</label>
                                <div class="mt-1">
                                    <select id="staffId" 
                                        name="staffId"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="" {{ $store->staffId == null ? 'selected' : '' }} disabled>Not Assigned</option>
                                        @foreach ($staff as $user)
                                            <option value="{{ $user->staffId }}" {{ $store->staffId == $user->staffId ? 'selected' : '' }}>
                                                {{ $user->staffName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Store
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
