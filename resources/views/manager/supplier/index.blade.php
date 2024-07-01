@extends('layouts.app')

@section('title', 'Supplier Management')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <h1 class="h3 mb-3 text-gray-800">Supplier Management</h1>
                <a href="{{ route('supplier.create') }}" class="btn btn-primary">Add New Supplier</a>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Supplier List
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Contact Info</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($suppliers as $supplier)
                                <tr>
                                    <td>{{ $supplier->supplierId }}</td>
                                    <td>{{ $supplier->supplierName }}</td>
                                    <td>{{ $supplier->contactInfo }}</td>
                                    <td>
                                        <a href="{{ route('supplier.edit', $supplier->supplierId) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('supplier.destroy', $supplier->supplierId) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
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
