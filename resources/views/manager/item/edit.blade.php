@extends('layouts.app')

@section('title', 'Edit Item')

@section('content')
    <div class="container">
        <h1 class="h3 mb-3 text-gray-800">Edit Item</h1>

        <form action="{{ route('item.update', $item->itemId) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="itemName">Item Name</label>
                <input type="text" id="itemName" name="itemName" class="form-control" value="{{ $item->itemName }}">
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="categoryId" class="form-control">
                    @foreach($categories as $category)
                        <option value="{{ $category->categoryId }}" {{ $item->categoryId == $category->categoryId ? 'selected' : '' }}>{{ $category->categoryName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control">{{ $item->description }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Item</button>
        </form>
    </div>
@endsection
