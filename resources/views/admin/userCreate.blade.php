@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <h1 class="h3 mb-3 text-gray-800">Add User</h1>
            </div>
        </div>
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            User Information
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="staffName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="staffName" name="staffName" value="{{ old('staffName') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
@endsection
