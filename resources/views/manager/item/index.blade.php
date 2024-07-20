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
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Inventory Quantity</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td>{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td>
                                        <td>{{ $item->itemName }}</td>
                                        <td>{{ $item->category->categoryName }}</td>
                                        <td>{{ $item->inventory->sum('quantity') }}</td>
                                        <td>
                                            <a href="{{ route('item.edit', $item->itemId) }}" class="btn btn-warning btn-sm"><i class="lni lni-pencil"></i></a>
                                            <button class="btn btn-danger delete-button btn-sm"
                                                    data-url="{{ route('item.destroy', $item->itemId) }}"><i class="lni lni-trash-can"></i>
                                            </button>
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
