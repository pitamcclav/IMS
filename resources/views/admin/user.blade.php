@extends('layouts.app')

@section('title', 'User Management')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <h1 class="h3 mb-3 text-gray-800">User Management</h1>
                <a href="{{ route('users.create') }}" class="btn btn-primary">Add New User</a>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        User List
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($staff as $user)
                                    <tr>
                                        <td>{{ $user->staffId }}</td>
                                        <td>{{ $user->staffName }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role }}</td>
                                        <td>
                                            <a href="{{ route('users.edit', $user->staffId) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('users.destroy', $user->staffId) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{-- {{ $staff->links('vendor.pagination.bootstrap-5') }} <!-- Pagination links --> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
