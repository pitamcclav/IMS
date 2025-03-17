<!-- Sidebar -->
<aside 
    class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform bg-gray-800 border-r border-gray-700" 
    x-data="sidebar"
    :class="{ 'transform -translate-x-full': !$store.sidebar.isOpen, 'transform translate-x-0': $store.sidebar.isOpen }"
    @keydown.escape="$store.sidebar.close()"
>
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-20 bg-gray-900 border-b border-gray-700">
        <a href="#" class="flex items-center space-x-3">
            <span class="text-xl font-semibold text-white">ERA IMS</span>
        </a>
    </div>

    <!-- Sidebar Content -->
    <nav class="h-[calc(100%-8rem)] px-3 py-4 overflow-y-auto text-gray-300">
        <ul class="space-y-2">
            @auth('staff')
                <li>
                    <a href="{{ (auth('staff')->user()->hasRole('admin') ? route('admin.dashboard') : (auth('staff')->user()->hasRole('staff') ? route('staff.dashboard') : route('manager.dashboard'))) }}"
                              class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 group {{ request()->routeIs(['admin.dashboard', 'staff.dashboard', 'manager.dashboard']) ? 'bg-gray-700 text-white' : '' }}">
                        <i class="lni lni-dashboard w-5 h-5 text-gray-400 transition group-hover:text-white"></i>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </li>
            @endauth

            <li>
                <a href="{{ route('requests.index') }}" 
                    class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 group {{ request()->routeIs('requests.index') ? 'bg-gray-700 text-white' : '' }}">
                    <i class="lni lni-envelope w-5 h-5 text-gray-400 transition group-hover:text-white"></i>
                    <span class="ml-3">Requests</span>
                </a>
            </li>

            @can('manage inventory')
                <li>
                    <a href="{{ route('inventory.index') }}" 
                        class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 group {{ request()->routeIs('inventory.index') ? 'bg-gray-700 text-white' : '' }}">
                        <i class="lni lni-list w-5 h-5 text-gray-400 transition group-hover:text-white"></i>
                        <span class="ml-3">Inventory</span>
                    </a>
                </li>
            @endcan

            @can('manage items')
                <li>
                    <a href="{{ route('item.index') }}" 
                        class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 group {{ request()->routeIs('item.index') ? 'bg-gray-700 text-white' : '' }}">
                        <i class="lni lni-grid w-5 h-5 text-gray-400 transition group-hover:text-white"></i>
                        <span class="ml-3">Items</span>
                    </a>
                </li>
            @endcan

            @can('manage categories')
                <li>
                    <a href="{{ route('category.index') }}" 
                        class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 group {{ request()->routeIs('category.index') ? 'bg-gray-700 text-white' : '' }}">
                        <i class="lni lni-package w-5 h-5 text-gray-400 transition group-hover:text-white"></i>
                        <span class="ml-3">Categories</span>
                    </a>
                </li>
            @endcan

            @can('manage suppliers')
                <li>
                    <a href="{{ route('supplier.index') }}" 
                        class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 group {{ request()->routeIs('supplier.index') ? 'bg-gray-700 text-white' : '' }}">
                        <i class="lni lni-users w-5 h-5 text-gray-400 transition group-hover:text-white"></i>
                        <span class="ml-3">Suppliers</span>
                    </a>
                </li>
            @endcan

            @can('manage staff')
                <li x-data="{ open: false }">
                    <button 
                        type="button" 
                        class="flex items-center w-full p-2 text-gray-300 rounded-lg hover:bg-gray-700 group"
                        @click="open = !open"
                        :aria-expanded="open"
                    >
                        <i class="lni lni-cog w-5 h-5 text-gray-400 transition group-hover:text-white"></i>
                        <span class="flex-1 ml-3 text-left">Administration</span>
                        <i class="lni w-5 h-5" :class="{ 'lni-chevron-up': open, 'lni-chevron-down': !open }"></i>
                    </button>
                    <ul 
                        x-show="open"
                        x-cloak
                        x-transition
                        class="px-5 pt-2 pb-0 space-y-1"
                    >
                        <li>
                            <a href="{{ route('users.index') }}" 
                                class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 group {{ request()->routeIs('users.index') ? 'bg-gray-700 text-white' : '' }}">
                                <i class="lni lni-users w-5 h-5 text-gray-400 transition group-hover:text-white"></i>
                                <span class="ml-3">Users</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('stores') }}" 
                                class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 group {{ request()->routeIs('stores') ? 'bg-gray-700 text-white' : '' }}">
                                <i class="lni lni-apartment w-5 h-5 text-gray-400 transition group-hover:text-white"></i>
                                <span class="ml-3">Stores</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('emailTemplates.index') }}" 
                                class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 group {{ request()->routeIs('emailTemplates.index') ? 'bg-gray-700 text-white' : '' }}">
                                <i class="lni lni-envelope w-5 h-5 text-gray-400 transition group-hover:text-white"></i>
                                <span class="ml-3">Email Templates</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
        </ul>
    </nav>

    <!-- Sidebar Footer -->
    <div class="absolute bottom-0 left-0 w-full border-t border-gray-700 bg-gray-800">
        <a href="{{ route('logout') }}" 
            class="flex items-center p-4 text-gray-300 hover:bg-gray-700 group">
            <i class="lni lni-exit w-5 h-5 text-gray-400 transition group-hover:text-white"></i>
            <span class="ml-3">
                @auth {{ Auth::user()->staffName }} @else Guest @endauth
            </span>
        </a>
    </div>
</aside>

<!-- External Toggle Button -->
<div x-data>
    <button 
        type="button"
        @click="$store.sidebar.toggle()"
        class="fixed top-4 left-4 z-50 p-2 rounded-lg bg-gray-700 text-white shadow-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500"
        :class="{ 'left-68': $store.sidebar.isOpen && window.innerWidth >= 768 }"
        aria-label="Toggle sidebar"
    >
        <i class="lni w-5 h-5" :class="{ 'lni-close': $store.sidebar.isOpen, 'lni-menu': !$store.sidebar.isOpen }"></i>
    </button>
</div>

<!-- Main Content -->
<main 
    x-data
    class="transition-all duration-300 ease-in-out" 
    :class="{ 'md:ml-64': $store.sidebar.isOpen, 'ml-0': !$store.sidebar.isOpen }"
>
    <!-- Your main content here -->
</main>