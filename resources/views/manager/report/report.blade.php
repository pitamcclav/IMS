@extends('layouts.app')

@section('title', 'Generate report')

@section('content')
    <div class="container">
        <h1 class="h3 mb-3 text-gray-800">Generate Reports</h1>

        <form action="{{ route('report.create') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="dateRange">Date Range</label>
                <input type="text" id="dateRange" name="dateRange" class="form-control daterangepicker">
            </div>
            <div class="form-group">
                <label for="store">Store</label>
                <select id="store" name="storeId" class="form-control">
                    @foreach($stores as $store)
                        <option value="{{ $store->storeId }}">{{ $store->storeName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="item">Item</label>
                <select id="item" name="itemId" class="form-control">
                    @foreach($items as $item)
                        <option value="{{ $item->itemId }}">{{ $item->itemName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="categoryId" class="form-control">
                    @foreach($categories as $category)
                        <option value="{{ $category->categoryId }}">{{ $category->categoryName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="staff">Staff</label>
                <select id="staff" name="staffId" class="form-control">
                    @foreach($staffs as $staff)
                        <option value="{{ $staff->staffId }}">{{ $staff->staffName }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Generate Report</button>
        </form>
    </div>
@endsection
