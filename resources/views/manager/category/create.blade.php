@extends('layouts.app')

@section('title', 'Add New Category')

@section('content')
    <div class="container">
        <h1 class="h3 mb-3 text-gray-800">Add New Category</h1>

        <form action="{{ route('category.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="categoryName">Category Name</label>
                <input type="text" id="categoryName" name="categoryName" class="form-control">
            </div>
            <div class="form-group">
                <label for="isReturnable">Is Returnable</label>
                <select id="isReturnable" name="isReturnable" class="form-control">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>
    </div>
@endsection
