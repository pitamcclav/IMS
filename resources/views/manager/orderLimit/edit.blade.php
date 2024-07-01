@extends('layouts.app')

@section('title', 'Edit Order Limit')

@section('content')
    <div class="container">
        <h1 class="h3 mb-3 text-gray-800">Edit Order Limit</h1>

        <form action="{{ route('orderLimit.update', $orderlimit->limitId) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="item">Item</label>
                <select id="item" name="itemId" class="form-control">
                    @foreach($items as $item)
                        <option value="{{ $item->itemId }}" {{ $orderlimit->itemId == $item->itemId ? 'selected' : '' }}>{{ $item->itemName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="orderLimit">Order Limit</label>
                <input type="number" id="orderLimit" name="orderLimit" class="form-control" value="{{ $orderlimit->orderLimit }}">
            </div>
            <div class="form-group">
                <label for="period">Period</label>
                <input type="text" id="period" name="period" class="form-control" value="{{ $orderlimit->period }}">
            </div>
            <button type="submit" class="btn btn-primary">Update Order Limit</button>
        </form>
    </div>
@endsection
