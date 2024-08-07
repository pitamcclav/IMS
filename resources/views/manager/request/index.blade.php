@extends('layouts.app')

@section('title', 'Request Management')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <h1 class="h3 mb-3 text-gray-800">Request Management</h1>
                <a href="{{ route('requests.create') }}" class="btn btn-sm btn-primary">Add New Request</a>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Request List
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Staff</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($requests as $index => $request)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $request->staff->staffName ?? 'N/A' }}</td>
                                    <td>
                                        <span class="status-label">
                                            <span class="status-dot status-{{ strtolower($request->status) }}"></span>

                                        </span>
                                    </td>
                                    <td>{{ $request->date }}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewRequestModal{{ $request->requestId }}">
                                            <i class="lni lni-eye"></i> <!-- Line Icon for 'eye' -->
                                        </button>
                                        @if(auth()->user()->hasRole('staff'))
                                            @if($request->status == 'pending')
                                                <a href="{{ route('requests.edit', $request->requestId) }}" class="btn btn-warning btn-sm">
                                                    <i class="lni lni-pencil"></i> <!-- Line Icon for 'pencil' -->
                                                </a>
                                                <button class="btn btn-danger btn-sm delete-button" data-url="{{ route('requests.destroy', $request->requestId) }}">
                                                    <i class="lni lni-trash-can"></i> <!-- Line Icon for 'trash' -->
                                                </button>
                                            @endif
                                        @elseif(auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin'))
                                            <a href="{{ route('requests.edit', $request->requestId) }}" class="btn btn-warning btn-sm">
                                                <i class="lni lni-pencil"></i> <!-- Line Icon for 'pencil' -->
                                            </a>
                                            <button class="btn btn-danger btn-sm delete-button" data-url="{{ route('requests.destroy', $request->requestId) }}">
                                                <i class="lni lni-trash-can"></i> <!-- Line Icon for 'trash' -->
                                            </button>
                                            <form action="{{ route('requests.updateStatus', $request->requestId) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                @if ($request->status == 'pending')
                                                    <input type="hidden" name="status" value="ready">
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="lni lni-checkmark"></i> <!-- Line Icon for 'checkmark' -->
                                                    </button>
                                                @elseif ($request->status == 'ready')
                                                    <input type="hidden" name="status" value="picked">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="lni lni-checkmark-circle"></i> <!-- Line Icon for 'checkmark-circle' -->
                                                    </button>
                                                @endif
                                            </form>
                                        @endif
                                    </td>

                                </tr>

                                <!-- Modal for Viewing Request Details -->
                                <div class="modal fade" id="viewRequestModal{{ $request->requestId }}" tabindex="-1" role="dialog" aria-labelledby="viewRequestModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="viewRequestModalLabel">Request Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Date:</strong> {{ $request->date }}</p>
                                                <p><strong>Status:</strong> {{ $request->status }}</p>
                                                <p><strong>Staff:</strong> {{ $request->staff->staffName ?? 'N/A' }}</p>
                                                <h5>Request Details</h5>
                                                <ul>
                                                    @foreach ($request->requestDetails as $detail)
                                                        <li>
                                                            <strong>Item:</strong> {{ $detail->item->itemName }} <br>
                                                            <strong>Quantity:</strong> {{ $detail->quantity }} <br>
                                                            <strong>Color:</strong> {{ $detail->color->colourName }} <br>
                                                            <strong>Size:</strong> {{ $detail->size->sizeValue }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal for Viewing Request Details -->
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $requests->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Optional: Add any JavaScript to run when the page is loaded
        });
    </script>
@endpush
