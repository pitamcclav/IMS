@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
    <div class="container">
        <h1 class="h3 mb-3 text-gray-800">Edit Supplier</h1>

        <form action="{{ route('supplier.update', $supplier->supplierId) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="supplierName">Supplier Name</label>
                <input type="text" id="supplierName" name="supplierName" class="form-control" value="{{ $supplier->supplierName }}">
            </div>
            <div class="form-group">
                <label for="contactInfo">Contact Info</label>
                <textarea id="contactInfo" name="contactInfo" class="form-control">{{ $supplier->contactInfo }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Supplier</button>
        </form>
    </div>
@endsection
