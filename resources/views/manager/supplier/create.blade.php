@extends('layouts.app')

@section('title', 'Add New Supplier')

@section('content')
    <div class="container">
        <h1 class="h3 mb-3 text-gray-800">Add New Supplier</h1>

        <form action="{{ route('supplier.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="supplierName">Supplier Name</label>
                <input type="text" id="supplierName" name="supplierName" class="form-control">
            </div>
            <div class="form-group">
                <label for="contactInfo">Contact Info</label>
                <textarea id="contactInfo" name="contactInfo" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Supplier</button>
        </form>
    </div>
@endsection
