@extends('layouts.app')

@section('title', 'Item Management')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <h1 class="h3 mb-3 text-gray-800">Item Management</h1>
                <a href="{{ route('item.create') }}" class="btn btn-primary">Add New Item</a>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Item List
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Inventory Quantity</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{ $item->itemId }}</td>
                                        <td>{{ $item->itemName }}</td>
                                        <td>{{ $item->category->categoryName }}</td>
                                        <td>{{ $item->inventory->sum('quantity') }}</td> <!-- Example: Summing up inventory quantities -->
                                        <td>
                                            <a href="{{ route('item.edit', $item->itemId) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('item.destroy', $item->itemId) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $items->links('vendor.pagination.bootstrap-5') }} <!-- Pagination links -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
