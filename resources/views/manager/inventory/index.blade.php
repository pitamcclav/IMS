@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Inventory</h1>
        <a href="{{ route('inventory.create') }}" class="btn btn-primary mb-3">Add New Inventory Item</a>
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr>
                <th>Inventory ID</th>
                <th>Item</th>
                <th>Color</th>
                <th>Size</th>
                <th>Quantity</th>
                <th>Initial</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @if (count($inventories) === 0)
                <tr>
                    <td colspan="7" class="text-center">No inventory items found.</td>
                </tr>
            @else
                @foreach ($inventories as $inventory)
                    <tr>
                        <td>{{ $inventory->inventoryId }}</td>
                        <td>{{ $inventory->item->itemName ?? 'N/A' }}</td>
                        <td>{{ $inventory->colour->colourName ?? 'N/A' }}</td>
                        <td>{{ $inventory->size->sizeValue ?? 'N/A' }}</td>
                        <td>{{ $inventory->quantity }}</td>
                        <td>{{ $inventory->initialQuantity }}</td>
                        <td>
                            <a href="{{ route('inventory.edit', $inventory->inventoryId) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('inventory.destroy', $inventory->inventoryId) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
    <div class="my-4 ">
        {{ $inventories->links('vendor.pagination.bootstrap-5') }}
    </div>
@endsection
