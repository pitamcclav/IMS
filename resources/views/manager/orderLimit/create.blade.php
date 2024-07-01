@extends('layouts.app')

@section('title', 'Add New Order Limit')

@section('content')
    <div class="container">
        <h1 class="h3 mb-3 text-gray-800">Add New Order Limit</h1>

        <form action="{{ route('orderLimit.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="item">Item</label>
                <select id="item" name="itemId" class="form-control">
                    @foreach($items as $item)
                        <option value="{{ $item->itemId }}">{{ $item->itemName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="orderLimit">Order Limit</label>
                <input type="number" id="orderLimit" name="orderLimit" class="form-control">
            </div>
            <div class="form-group">
                <label for="period">Period</label>
                <input type="text" id="period" name="period" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Add Order Limit</button>
        </form>
    </div>
@endsection
