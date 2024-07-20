@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Inventory</h1>
        <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm my-3 ">Add New Inventory Item</a>
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Color</th>
                <th>Size</th>
                <th>Quantity</th>
                <th>Initial Quantity</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @if ($inventories->isEmpty())
                <tr>
                    <td colspan="7" class="text-center">No inventory items found.</td>
                </tr>
            @else
                @foreach ($inventories as $index => $inventory)
                    <tr>
                        <td>{{ $index + 1 }}</td> <!-- Use $index + 1 for serial number -->
                        <td>{{ $inventory->item->itemName ?? 'N/A' }}</td>
                        <td>{{ $inventory->colour->colourName ?? 'N/A' }}</td>
                        <td>{{ $inventory->size->sizeValue ?? 'N/A' }}</td>
                        <td>{{ $inventory->quantity }}</td>
                        <td>{{ $inventory->initialQuantity }}</td>
                        <td>
                            <a href="{{ route('inventory.edit', $inventory->inventoryId) }}" class="btn btn-primary btn-sm">
                                <i class="lni lni-pencil"></i> <!-- Line Icon for 'pencil' -->
                            </a>
                            <button class="btn btn-danger btn-sm delete-button" data-url="{{ route('inventory.destroy', $inventory->inventoryId) }}">
                                <i class="lni lni-trash-can"></i> <!-- Line Icon for 'trash' -->
                            </button>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
    <div class="my-4">
        {{ $inventories->links('vendor.pagination.bootstrap-5') }}
    </div>
@endsection
