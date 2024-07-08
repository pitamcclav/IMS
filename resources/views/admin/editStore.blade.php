@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
    <div class="container">
        <h1 class="h3 mb-3 text-gray-800">Edit Category</h1>

        <form action="{{ route('stores.update', $store->storeId) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="storeName">Store Name</label>
                <input type="text" id="storeName" name="storeName" class="form-control" value="{{ $store->storeName }}">
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" class="form-control" value="{{ $store->location }}">
            </div>
            <div class="form-group">
                <label for="staffId">Staff In-charge</label>
                <select id="staffId" name="staffId" class="form-control">
                    <option value="" disabled>Select staff</option>
                    <option value="" {{ $store->staffId == null ? 'selected' : '' }} disabled>Not Assigned</option>
                    @foreach ($staff as $user)
                        <option value="{{ $user->staffId }}" {{ $store->staffId == $user->staffId ? 'selected' : '' }}>{{ $user->staffName }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Category</button>
        </form>
    </div>
@endsection
