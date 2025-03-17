@extends('layouts.base')

@section('body')
    <div class="min-h-screen bg-gray-100">
        @include('partials.sidebar')
        
        <!-- Main Content Area -->
        <div 
            x-data
            class="no-transition transition-all duration-300 ease-in-out"
            :class="{ 'lg:pl-64': $store.sidebar.isOpen, 'pl-7': !$store.sidebar.isOpen }"
            x-init="setTimeout(() => $el.classList.remove('no-transition'), 0)"
        >
            
            <!-- Page Content -->
            <main class="pt-8">
                <!-- Laravel Session Notifications -->
                <div class="fixed inset-x-0 top-20 flex justify-center items-start z-50 px-4">
                    <div class="w-full max-w-md space-y-4">
                        @if (session('success'))
                            <div class="w-full bg-green-50 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                                <div class="p-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="lni lni-checkmark-circle text-green-400"></i>
                                        </div>
                                        <div class="ml-3 w-0 flex-1 pt-0.5">
                                            <p class="text-sm font-medium text-green-800">
                                                {{ session('success') }}
                                            </p>
                                        </div>
                                        <div class="ml-4 flex-shrink-0 flex">
                                            <button onclick="this.closest('div.w-full').remove()"
                                                    class="bg-transparent rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <span class="sr-only">Close</span>
                                                <i class="lni lni-close h-5 w-5"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="w-full bg-red-50 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                                <div class="p-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="lni lni-close text-red-400"></i>
                                        </div>
                                        <div class="ml-3 w-0 flex-1 pt-0.5">
                                            <p class="text-sm font-medium text-red-800">
                                                {{ session('error') }}
                                            </p>
                                        </div>
                                        <div class="ml-4 flex-shrink-0 flex">
                                            <button onclick="this.closest('div.w-full').remove()"
                                                    class="bg-transparent rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <span class="sr-only">Close</span>
                                                <i class="lni lni-close h-5 w-5"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="w-full bg-red-50 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                                <div class="p-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="lni lni-warning text-red-400"></i>
                                        </div>
                                        <div class="ml-3 w-0 flex-1 pt-0.5">
                                            <ul class="list-disc list-inside text-sm font-medium text-red-800">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="ml-4 flex-shrink-0 flex">
                                            <button onclick="this.closest('div.w-full').remove()"
                                                    class="bg-transparent rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <span class="sr-only">Close</span>
                                                <i class="lni lni-close h-5 w-5"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Alpine.js Dynamic Notifications -->
                <div class="fixed inset-x-0 top-20 flex justify-center items-start z-50 px-4 mt-20" 
                     x-data 
                     @notification.window="Alpine.store('notifications').add($event.detail)">
                    <div class="w-full max-w-md space-y-4">
                        <template x-for="notification in $store.notifications.items" :key="notification.id">
                            <div x-show="true"
                                    x-transition:enter="transform ease-out duration-300 transition"
                                    x-transition:enter-start="translate-y-2 opacity-0"
                                    x-transition:enter-end="translate-y-0 opacity-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
                                    :class="{
                                        'bg-green-50': notification.type === 'success',
                                        'bg-red-50': notification.type === 'error',
                                        'bg-yellow-50': notification.type === 'warning',
                                        'bg-blue-50': notification.type === 'info'
                                    }">
                                    <div class="p-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <template x-if="notification.type === 'success'">
                                                    <i class="lni lni-checkmark-circle text-green-400"></i>
                                                </template>
                                                <template x-if="notification.type === 'error'">
                                                    <i class="lni lni-close text-red-400"></i>
                                                </template>
                                                <template x-if="notification.type === 'warning'">
                                                    <i class="lni lni-warning text-yellow-400"></i>
                                                </template>
                                                <template x-if="notification.type === 'info'">
                                                    <i class="lni lni-information text-blue-400"></i>
                                                </template>
                                            </div>
                                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                                <p x-text="notification.message"
                                                :class="{
                                                    'text-green-800': notification.type === 'success',
                                                    'text-red-800': notification.type === 'error',
                                                    'text-yellow-800': notification.type === 'warning',
                                                    'text-blue-800': notification.type === 'info'
                                                }"
                                                class="text-sm font-medium"></p>
                                            </div>
                                            <div class="ml-4 flex-shrink-0 flex">
                                                <button @click="$store.notifications.remove(notification.id)"
                                                        class="bg-transparent rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <span class="sr-only">Close</span>
                                                    <i class="lni lni-close h-5 w-5"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                
                <div class="px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        // Auto-hide Laravel notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', () => {
            const notifications = document.querySelectorAll('.bg-green-50, .bg-red-50');
            notifications.forEach(notification => {
                setTimeout(() => {
                    notification.remove();
                }, 5000);
            });
        });
    </script>
@endsection
