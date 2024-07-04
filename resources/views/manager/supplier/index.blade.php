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
                                <!-- Supplier Row -->
                                <tr>
                                    <td>{{ $supplier->supplierId }}</td>
                                    <td>{{ $supplier->supplierName }}</td>
                                    <td>{{ $supplier->contactInfo }}</td>
                                    <td>
                                        <!-- View Button -->
                                        <button class="btn btn-info btn-sm view-btn" data-supplier="{{json_encode($supplier)}}" data-bs-toggle="modal" data-bs-target="#viewSupplierModal">
                                            View
                                        </button>
                                        <!-- Edit Button -->
                                        <a href="{{ route('supplier.edit', $supplier->supplierId) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <!-- Delete Form -->
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

    <!-- Modal for Viewing Supplier Details -->
    <div class="modal fade" id="viewSupplierModal" tabindex="-1" role="dialog" aria-labelledby="viewSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewSupplierModalLabel">Supplier Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Id:</strong> <span id="supplierId"></span></p>
                    <p><strong>Name:</strong> <span id="supplierName"></span></p>
                    <p><strong>Contact:</strong> <span id="supplierContact"></span></p>
                    <h5>Supply Details</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Supply Date</th>
                        </tr>
                        </thead>
                        <tbody id="supplyDetails">
                        <!-- Supply details will be dynamically populated here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{asset('js/supplier.js')}}"></script>
@endsection




