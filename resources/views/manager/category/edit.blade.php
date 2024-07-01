@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
    <div class="container">
        <h1 class="h3 mb-3 text-gray-800">Edit Category</h1>

        <form action="{{ route('category.update', $category->categoryId) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="categoryName">Category Name</label>
                <input type="text" id="categoryName" name="categoryName" class="form-control" value="{{ $category->categoryName }}">
            </div>
            <div class="form-group">
                <label for="isReturnable">Is Returnable</label>
                <select id="isReturnable" name="isReturnable" class="form-control">
                    <option value="1" {{ $category->isReturnable ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ !$category->isReturnable ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Category</button>
        </form>
    </div>
@endsection
