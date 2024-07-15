@extends('layouts.app')

@section('title', 'Add New Item')

@section('content')
    <div class="container">
        <h1 class="h3 mb-3 text-gray-800">Add New Item</h1>

        <form action="{{ route('item.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="itemName">Item Name</label>
                <input type="text" id="itemName" name="itemName" class="form-control" placeholder="Item Name">
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="categoryId" class="form-control">
                    <option value="" disabled>Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->categoryId }}">{{ $category->categoryName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" placeholder="about item" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Item</button>
        </form>
    </div>
@endsection
