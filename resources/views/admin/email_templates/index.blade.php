@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Email Templates</h1>

        <a href="{{ route('emailTemplates.create') }}" class="btn btn-primary mb-3">Create New Template</a>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @if($templates->isEmpty())
                <tr>
                    <td colspan="4">No templates found.</td>
                </tr>
            @else
            @foreach($templates as $template)
                <tr>
                    <td>{{ $template->id }}</td>
                    <td>{{ $template->name }}</td>
                    <td>{{ $template->type }}</td>
                    <td>
                        <a href="{{ route('emailTemplates.edit', $template->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('emailTemplates.destroy', $template->id) }}" method="POST" style="display:inline-block;">
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
@endsection
