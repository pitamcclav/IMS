@extends('layouts.app')

@section('title', 'Edit Request')

@section('content')
    <div class="container">
        <h1 class="h3 mb-3 text-gray-800">Edit Request</h1>

        <form action="{{ route('requests.update', $request->requestId) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="staff">Staff</label>
                <select id="staff" name="staffId" class="form-control">
                    @foreach($staffs as $staff)
                        <option value="{{ $staff->staffId }}" {{ $request->staffId == $staff->staffId ? 'selected' : '' }}>{{ $staff->staffName }}</option>
                    @endforeach
                </select>
            </div>
            <hr>
            <div id="requestDetailsContainer">
                @foreach($request->requestDetails as $index => $detail)
                    <div class="requestDetail mb-3">
                        <label for="item">Item</label>
                        <select name="itemIds[]" class="form-control item-select">
                            @foreach($items as $item)
                                <option value="{{ $item->itemId }}" {{ $detail->itemId == $item->itemId ? 'selected' : '' }}>{{ $item->itemName }}</option>
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

                                            @foreach($sizes as $size)
                                                <option value="{{ $size->sizeId }}" {{ $detail->sizeId == $size->sizeId ? 'selected' : '' }}>{{ $size->sizeValue }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="colourIds[]" class="form-control">
                                            @foreach($colours as $colour)
                                                <option value="{{ $colour->colourId }}" {{ $detail->colourId == $colour->colourId ? 'selected' : '' }}>{{ $colour->colourName }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <button type="button" class="btn btn-outline-secondary minus-quantity">-</button>
                                            <input type="number" name="quantities[]" class="form-control mx-2 text-center" value="{{ $detail->quantity }}" min="1">
                                            <button type="button" class="btn btn-outline-secondary plus-quantity">+</button>
                                        </div>
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-success add-row-btn">+</button>
                                        <button type="button" class="btn btn-danger remove-row-btn">-</button>
                                        <button type="button" class="btn btn-danger removeDetail "><i class="lni lni-trash-can"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-secondary my-4" id="addItemBtn">Add Item</button>
            <button type="submit" class="btn btn-primary">Update Request</button>
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
