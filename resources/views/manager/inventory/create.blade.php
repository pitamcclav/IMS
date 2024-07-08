@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Add New Inventory Item</h1>
        <form action="{{ route('inventory.store') }}" method="POST" >
            @csrf

            <div class="form-group">
                <label for="itemid">Item</label>
                <select name="itemid" class="form-control" id="itemid" required>
                    <option value="" disabled selected>Select Item</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->itemId }}">{{ $item->itemName }}</option>
                    @endforeach
                    <option value="new">New Item</option>
                </select>
            </div>

            <div class="form-group">
                <label for="supplierid">Supplier</label>
                <select name="supplierid" class="form-control" id="supplierid" required>
                    <option value="" disabled selected>Select Supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->supplierId }}">{{ $supplier->supplierName }}</option>
                    @endforeach
                    <option value="new">New Supplier</option>
                </select>
            </div>

            <hr>
            <div id="inventoryDetailsContainer">
                <div class="inventoryDetail mb-3">
                    <label>Variants</label>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Size</th>
                            <th>Colour</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="variant-row">
                            <td>
                                <select name="sizeIds[]" class="form-control" id="sizeid" required>
                                    <option value="" disabled selected>Select Size</option>
                                    @foreach($sizes as $size)
                                        <option value="{{ $size->sizeId }}">{{ $size->sizeValue }}</option>
                                    @endforeach
                                    <option value="new">New Size</option>
                                </select>
                            </td>
                            <td>
                                <select name="colourIds[]" class="form-control" id="colourid" required>
                                    <option value="" disabled selected>Select Colour</option>
                                    @foreach($colours as $colour)
                                        <option value="{{ $colour->colourId }}">{{ $colour->colourName }}</option>
                                    @endforeach
                                    <option value="new">New Colour</option>
                                </select>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-secondary minus-quantity">-</button>
                                    <input type="number" name="quantities[]" class="form-control mx-2 text-center" value="1" min="1">
                                    <button type="button" class="btn btn-outline-secondary plus-quantity">+</button>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger remove-row-btn">-</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <button type="button" class="btn btn-secondary my-4" id="addInventoryBtn">Add Variant</button>
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
    <script>
        const sizes = @json($sizes);
        const colours = @json($colours);
    </script>
    <script src="{{ asset('js/inventory.js') }}"></script>
@endsection
