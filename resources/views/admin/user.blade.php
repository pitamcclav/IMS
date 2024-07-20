<!-- resources/views/user-management.blade.php -->

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
                                        <td>
                                            @if($user->roles->isNotEmpty())
                                                @foreach($user->roles as $role)
                                                    {{ $role->name }}
                                                @endforeach
                                            @else
                                                <button type="button" class="btn btn-primary btn-sm assign-role-btn"
                                                        data-userid="{{ $user->staffId }}" data-username="{{ $user->staffName }}">Assign</button>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('users.edit', $user->staffId) }}" class="btn btn-warning btn-sm"><i class="lni lni-pencil"></i></a>

                                                <button type="submit" class="btn btn-danger delete-button btn-sm" data-url="{{ route('users.destroy', $user->staffId) }}"><i class="lni lni-trash-can"></i></button>

                                            @if($user->roles->isNotEmpty())
                                                <form action="{{ route('roles.revoke', $user->staffId) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to revoke roles for this user?')">Revoke</button>
                                                </form>
                                            @endif
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

    <!-- Assign Role Modal -->
    @include('partials.modals.assign-role')
@endsection

@section('scripts')
    <script src="{{asset('js/admin.js')}}">
    </script>
@endsection
