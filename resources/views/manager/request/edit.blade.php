@extends('layouts.app')

@section('title', 'Edit Request')

@section('content')
    <div class="container">
        <h1 class="h3 mb-3 text-gray-800">Edit Request</h1>

        <form action="{{ route('requests.update', $request->requestId) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="requestDetails">Request Details</label>
                <div id="requestDetails">
                    @foreach($request->requestDetails as $index => $detail)
                        <div class="requestDetail mb-3">
                            <label for="item">Item</label>
                            <select name="itemIds[]" class="form-control mb-2">
                                @foreach($items as $item)
                                    <option value="{{ $item->itemId }}" {{ $detail->itemId == $item->itemId ? 'selected' : '' }}>{{ $item->itemName }}</option>
                                @endforeach
                            </select>
                            <label for="quantity">Quantity</label>
                            <input type="number" name="quantities[]" class="form-control mb-2" value="{{ $detail->quantity }}" placeholder="Quantity">
                            <label for="colour">Colour</label>
                            <select name="colourIds[]" class="form-control mb-2">
                                @foreach($colours as $colour)
                                    <option value="{{ $colour->colourId }}" {{ $detail->colourId == $colour->colourId ? 'selected' : '' }}>{{ $colour->colourName }}</option>
                                @endforeach
                            </select>
                            <label for="size">Size</label>
                            <select name="sizeIds[]" class="form-control mb-2">
                                @foreach($sizes as $size)
                                    <option value="{{ $size->sizeId }}" {{ $detail->sizeId == $size->sizeId ? 'selected' : '' }}>{{ $size->sizeValue }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-danger btn-sm removeDetail">Remove</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="addRequestDetailBtn" class="btn btn-secondary my-4">Add More</button>
            </div>
            <button type="submit" class="btn btn-primary">Update Request</button>
        </form>

        <!-- Hidden options for dynamically adding request details -->
        <div id="itemOptions" style="display: none;">
            @foreach($items as $item)
                <option value="{{ $item->itemId }}">{{ $item->itemName }}</option>
            @endforeach
        </div>
        <div id="colourOptions" style="display: none;">
            @foreach($colours as $colour)
                <option value="{{ $colour->colourId }}">{{ $colour->colourName }}</option>
            @endforeach
        </div>
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
