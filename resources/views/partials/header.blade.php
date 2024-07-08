<aside id="sidebar" class="expand">
    <div class="d-flex align-items-center">
        <button class="toggle-btn" type="button">
            <i class="lni lni-menu"></i>
        </button>
        <div class="sidebar-logo">
            <a href="#">ERA IMS</a>
        </div>
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-item">
            <a href="@if (Auth::guard('staff')->check() && Auth::guard('staff')->user()->role == 'admin') {{ route('admin.dashboard') }}
                     @elseif (Auth::guard('staff')->check() && Auth::guard('staff')->user()->role == 'manager') {{ route('manager.dashboard') }}
                     @elseif (Auth::guard('staff')->check() && Auth::guard('staff')->user()->role == 'staff') {{ route('staff.dashboard') }}
                     @endif" class="sidebar-link">
                <i class="lni lni-dashboard"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
               data-bs-target="#requests" aria-expanded="false" aria-controls="requests">
                <i class="lni lni-envelope"></i>
                <span>Request Management</span>
            </a>
            <ul id="requests" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="{{ route('requests.index') }}" class="sidebar-link">Request List<i class="lni lni-chevron-right"></i></a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('requests.create') }}" class="sidebar-link">Add New Request<i class="lni lni-chevron-right"></i></a>
                </li>
            </ul>
        </li>
        @if (Auth::guard('staff')->check() && Auth::guard('staff')->user()->role == 'manager')
            <!-- Manager-specific items -->
            <li class="sidebar-item">
                <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                   data-bs-target="#categories" aria-expanded="false" aria-controls="categories">
                    <i class="lni lni-package"></i>
                    <span>Category Management</span>
                </a>
                <ul id="categories" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a href="{{ route('category.index') }}" class="sidebar-link">Category List<i class="lni lni-chevron-right"></i></a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('category.create') }}" class="sidebar-link">Add New Category<i class="lni lni-chevron-right"></i></a>
                    </li>
                </ul>
            </li>
        @endif

        @if (Auth::guard('staff')->check() && in_array(Auth::guard('staff')->user()->role, ['admin', 'manager']))
            <!-- Common items for Admin and Manager -->
            <li class="sidebar-item">
                <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                   data-bs-target="#inventory" aria-expanded="false" aria-controls="inventory">
                    <i class="lni lni-list"></i>
                    <span>Inventory Management</span>
                </a>
                <ul id="inventory" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a href="{{ route('inventory.index') }}" class="sidebar-link">Inventory List<i class="lni lni-chevron-right"></i></a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('inventory.create') }}" class="sidebar-link">Add New Inventory<i class="lni lni-chevron-right"></i></a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item">
                <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                   data-bs-target="#items" aria-expanded="false" aria-controls="items">
                    <i class="lni lni-grid"></i>
                    <span>Item Management</span>
                </a>
                <ul id="items" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a href="{{route('item.index')}}" class="sidebar-link">Item List<i class="lni lni-chevron-right"></i></a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{route('item.create')}}" class="sidebar-link">Add New Item<i class="lni lni-chevron-right"></i></a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                   data-bs-target="#suppliers" aria-expanded="false" aria-controls="suppliers">
                    <i class="lni lni-users"></i>
                    <span>Supplier Management</span>
                </a>
                <ul id="suppliers" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a href="{{route('supplier.index')}}" class="sidebar-link">Supplier List<i class="lni lni-chevron-right"></i></a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{route('supplier.create')}}" class="sidebar-link">Add New Supplier<i class="lni lni-chevron-right"></i></a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                   data-bs-target="#order-limits" aria-expanded="false" aria-controls="order-limits">
                    <i class="lni lni-layers"></i>
                    <span>Order Limits </span>
                </a>
                <ul id="order-limits" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a href="{{route('orderLimit.index')}}" class="sidebar-link">Order Limits List<i class="lni lni-chevron-right"></i></a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{route('orderLimit.create')}}" class="sidebar-link">Add New Order Limit<i class="lni lni-chevron-right"></i></a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('report.index') }}" class="sidebar-link">
                    <i class="lni lni-files"></i>
                    <span>Reports</span>
                </a>
            </li>
        @endif

        @if (Auth::guard('staff')->check() && Auth::guard('staff')->user()->role == 'admin')
            <!-- Admin-specific items -->
            <li class="sidebar-item">
                <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                   data-bs-target="#user-management" aria-expanded="false" aria-controls="user-management">
                    <i class="lni lni-users"></i>
                    <span>User Management</span>
                </a>
                <ul id="user-management" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a href="{{route('users.index')}}" class="sidebar-link">Users<i class="lni lni-chevron-right"></i></a>
                    </li>
                    <li class="sidebar-item">
                        <a href="" class="sidebar-link">Roles<i class="lni lni-chevron-right"></i></a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{route('stores')}}" class="sidebar-link">Stores<i class="lni lni-chevron-right"></i></a>
                    </li>
                </ul>
            </li>
            <!-- Additional Admin Specific Items -->
        @endif



        @if (Auth::guard('staff')->check() && Auth::guard('staff')->user()->role == 'staff')
            <!-- Staff-specific items -->

        @endif


    </ul>
    <div class="sidebar-footer">
        <a href="{{ route('logout') }}" class="sidebar-link">
            <i class="lni lni-exit"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
