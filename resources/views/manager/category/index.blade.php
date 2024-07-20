@extends('layouts.app')

@section('title', 'Category Management')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <h1 class="h3 mb-3 text-gray-800">Category Management</h1>
                <a href="{{ route('category.create') }}" class="btn btn-sm btn-primary">Add New Category</a>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Category List
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Is Returnable</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($categories as $index => $category)
                                <tr>
                                    <td>{{ $index + 1 }}</td> <!-- Simple index increment -->
                                    <td>{{ $category->categoryName }}</td>
                                    <td>{{ $category->isReturnable ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <a href="{{ route('category.edit', $category->categoryId) }}" class="btn btn-warning btn-sm"><i class="lni lni-pencil"></i></a>
                                        <button class="btn btn-sm btn-danger delete-button"
                                                data-url="{{ route('category.destroy', $category->categoryId) }}"><i class="lni lni-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
