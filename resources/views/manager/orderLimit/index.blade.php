@extends('layouts.app')

@section('title', 'Order Limits Management')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <h1 class="h3 mb-3 text-gray-800">Order Limits Management</h1>
                <a href="{{ route('orderLimit.create') }}" class="btn btn-primary">Add New Order Limit</a>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Order Limits List
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item</th>
                                <th>Order Limit</th>
                                <th>Period</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orderlimits as $orderlimit)
                                <tr>
                                    <td>{{ $orderlimit->limitId }}</td>
                                    <td>{{ $orderlimit->item->itemName ?? 'N/A'}}</td>
                                    <td>{{ $orderlimit->orderLimit }}</td>
                                    <td>{{ $orderlimit->period }}</td>
                                    <td>
                                        <a href="{{ route('orderLimit.edit', $orderlimit->limitId) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <button class="btn btn-danger delete-button"
                                                data-url="{{ route('limit.destroy', $limit->limitId) }}">Delete
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
