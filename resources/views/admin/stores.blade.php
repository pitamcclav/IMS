    @extends('layouts.app')

@section('title', 'Stores')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>Stores</h1>
                <button class="btn btn-primary btn-sm"  id="newStore" data-bs-toggle="modal" data-bs-target="#newStoreModal">Add
                    New Store
                </button>
                <table class="table" id="storeTable">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>In-charge</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($stores->isEmpty())
                        <tr>
                            <td colspan="4">No stores found.</td>
                        </tr>
                    @else

                        @foreach ($stores as $store)
                            <tr>
                                <td>{{ $store->storeId }}</td>
                                <td>{{ $store->storeName }}</td>
                                <td>{{ $store->location }}</td>
                                <td>{{ $store->manager->staffName ?? 'not assigned'}}</td>
                                <td>
                                    <a href="{{ route('stores.edit', $store->storeId) }}" class="btn btn-primary btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm delete-button"
                                                data-url="{{ route('stores.delete', $store->storeId) }}">Delete
                                        </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('partials.modals.new-store')


@endsection

@section('scripts')
    <script src="{{asset('js/stores.js')}}">
    </script>
@endsection
