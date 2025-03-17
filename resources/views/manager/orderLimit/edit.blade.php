@extends('layouts.app')

@section('title', 'Edit Order Limit')

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Edit Order Limit</h2>
            <p class="mt-2 text-sm text-gray-600">Modify order limit settings</p>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden p-6">
            <form action="{{ route('orderLimit.update', $orderlimit->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="itemId" class="block text-sm font-medium text-gray-700">Item</label>
                        <select name="itemId" id="itemId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required>
                            @foreach($items as $item)
                                <option value="{{ $item->itemId }}" {{ $orderlimit->itemId == $item->itemId ? 'selected' : '' }}>
                                    {{ $item->itemName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="orderLimit" class="block text-sm font-medium text-gray-700">Order Limit</label>
                        <input type="number" name="orderLimit" id="orderLimit" min="1" 
                               value="{{ $orderlimit->orderLimit }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               required>
                    </div>

                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700">Period</label>
                        <select name="period" id="period" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required>
                            <option value="daily" {{ $orderlimit->period === 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ $orderlimit->period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ $orderlimit->period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="lni lni-save mr-2"></i>
                        Update Order Limit
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
