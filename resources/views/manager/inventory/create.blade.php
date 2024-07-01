@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Add New Inventory Item</h1>
        <form action="{{ route('inventory.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="itemid">Item</label>
                <select name="itemid" class="form-control" id="itemid">
                    <option value="" disabled selected>Select Item</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->itemId }}">{{ $item->itemName }}</option>
                    @endforeach
                    <option value="new">New Item</option>
                </select>
            </div>
            <div class="form-group">
                <label for="colourid">Color</label>
                <select name="colourid" class="form-control" id="colourid">
                    <option value="" disabled selected>Select Color</option>
                    @foreach ($colors as $color)
                        <option value="{{ $color->colourId }}">{{ $color->colourName }}</option>
                    @endforeach
                    <option value="new">New Color</option>
                </select>
            </div>
            <div class="form-group">
                <label for="sizeid">Size</label>
                <select name="sizeid" class="form-control" id="sizeid">
                    <option value="" disabled selected>Select Size</option>
                    @foreach ($sizes as $size)
                        <option value="{{ $size->sizeId }}">{{ $size->sizeValue }}</option>
                    @endforeach
                    <option value="new">New Size</option>
                </select>
            </div>
            <div class="form-group">
                <label for="supplierid">Supplier</label>
                <select name="supplierid" class="form-control" id="supplierid">
                    <option value="" disabled selected>Select Supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->supplierId }}">{{ $supplier->supplierName }}</option>
                    @endforeach
                    <option value="new">New Supplier</option>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" class="form-control" required placeholder="Enter Quantity">
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>

    <!-- Modals for adding new items, colors, sizes, and suppliers -->
    @include('partials.modals.new-item')
    @include('partials.modals.new-colour')
    @include('partials.modals.new-size')
    @include('partials.modals.new-supplier')

@endsection

@section('scripts')
    <script src="{{ asset('js/inventory.js') }}"></script>
@endsection
