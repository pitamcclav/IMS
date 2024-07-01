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
                    @foreach($staffs as $staff)
                        <option value="{{ $staff->staffId }}">{{ $staff->staffName }}</option>
                    @endforeach
                </select>
            </div>
            <hr>
            <div class="form-group">
                <label for="requestDetails">Request Details</label>
                <div id="requestDetails">
                    <div class="requestDetail">
                        <label for="item">Item</label>
                        <select name="itemIds[]" class="form-control">
                            @foreach($items as $item)
                                <option value="{{ $item->itemId }}">{{ $item->itemName }}</option>
                            @endforeach
                        </select>
                        <label for="quantity">Quantity</label>
                        <input type="number" name="quantities[]" class="form-control" placeholder="Quantity">
                        <label for="colourOptions">Colour</label>
                        <select name="colourIds[]" class="form-control">
                            @foreach($colours as $colour)
                                <option value="{{ $colour->colourId }}">{{ $colour->colourName }}</option>
                            @endforeach
                        </select>
                        <label for="sizeOptions">Size</label>
                        <select name="sizeIds[]" class="form-control">
                            @foreach($sizes as $size)
                                <option value="{{ $size->sizeId }}">{{ $size->sizeValue }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr>
                </div>
                <button type="button" class="btn btn-secondary my-4" id="addRequestDetailBtn">Add More</button>
            </div>
            <button type="submit" class="btn btn-primary">Add Request</button>
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
