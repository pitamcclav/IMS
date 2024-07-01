@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Inventory Item</h1>
        <form action="{{ route('inventory.update', $inventory->inventoryId) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="itemid">Item</label>
                <select name="itemid" class="form-control">
                    @foreach ($items as $item)
                        <option value="{{ $item->itemId }}" {{ $inventory->itemId == $item->itemId ? 'selected' : '' }}>{{ $item->itemName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="colourid">Color</label>
                <select name="colourid" class="form-control">
                    @foreach ($colors as $color)
                        <option value="{{ $color->colourId }}" {{ $inventory->colourId == $color->colourId ? 'selected' : '' }}>{{ $color->colourName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="sizeid">Size</label>
                <select name="sizeid" class="form-control">
                    @foreach ($sizes as $size)
                        <option value="{{ $size->sizeId }}" {{ $inventory->sizeId == $size->sizeId ? 'selected' : '' }}>{{ $size->sizeValue }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" class="form-control" value="{{ $inventory->quantity }}" required>
            </div>
            <div class="form-group">
                <label for="initialQuantity">Initial Quantity</label>
                <input type="number" name="initialQuantity" class="form-control" value="{{ $inventory->initialQuantity }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
