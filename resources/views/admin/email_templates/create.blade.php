@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Create Email Template</h1>

        <form action="{{ route('emailTemplates.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Template Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="name" required>
            </div>

            <div class="form-group">
                <label for="subject">Template Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" placeholder="subject" required>
            </div>

            <div class="form-group">
                <label for="type">Store</label>
                <select class="form-control" id="type" name="store" required>
                    <option value="" disabled selected>Select store</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->storeId }}">{{ $store->storeName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="type">Template Type</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="" disabled selected>Select type</option>
                    <option value="request_created">Request Created</option>
                    <option value="status_changed">Status Changed</option>
                    <option value="low_stock">Low Stock</option>
                </select>
            </div>

            <div class="form-group">
                <label for="body">Template Content</label>
                <textarea class="form-control" id="body" name="body" rows="10" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Create Template</button>
        </form>
    </div>
@endsection
