@extends('layouts.app')

@section('title', 'Add New Request')

@section('content')
    <div class="container">
        <h1 class="h3 mb-3 text-gray-800">Add New Request</h1>

        <form action="{{ route('requests.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="staff">Staff</label>
                <select id="staff" name="staffId" class="form-control">
                    <option value="" disabled selected>Select Staff</option>
                    @foreach($staffs as $staff)
                        <option value="{{ $staff->staffId }}">{{ $staff->staffName }}</option>
                    @endforeach
                </select>
            </div>
            <hr>
            <div id="requestDetailsContainer">
                <div class="requestDetail mb-3">
                    <label for="item">Item</label>
                    <select name="itemIds[]" class="form-control item-select">
                        <option value="" disabled selected>Select Item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->itemId }}">{{ $item->itemName }}</option>
                        @endforeach
                    </select>

                    <div class="item-variants">
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
                                    <select name="sizeIds[]" class="form-control">
                                        <option value="" disabled selected>Select Size</option>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->sizeId }}">{{ $size->sizeValue }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="colourIds[]" class="form-control">
                                        <option value="" disabled selected>Select Colour</option>
                                        @foreach($colours as $colour)
                                            <option value="{{ $colour->colourId }}">{{ $colour->colourName }}</option>
                                        @endforeach
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
                                    <button type="button" class="btn btn-success add-row-btn">+</button>
                                    <button type="button" class="btn btn-danger remove-row-btn">-</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary my-4" id="addItemBtn">Add Item</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <!-- Hidden elements for options -->
        <label for="itemOptions" style="display: none;">Item Options</label>
        <div id="itemOptions" style="display: none;">
            @foreach($items as $item)
                <option value="{{ $item->itemId }}">{{ $item->itemName }}</option>
            @endforeach
        </div>
        <label for="colourOptions" style="display: none;">Colour Options</label>
        <div id="colourOptions" style="display: none;">
            @foreach($colours as $colour)
                <option value="{{ $colour->colourId }}">{{ $colour->colourName }}</option>
            @endforeach
        </div>
        <label for="sizeOptions" style="display: none;">Size Options</label>
        <div id="sizeOptions" style="display: none;">
            @foreach($sizes as $size)
                <option value="{{ $size->sizeId }}">{{ $size->sizeValue }}</option>
            @endforeach
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/request.js') }}"></script>
@endsection
