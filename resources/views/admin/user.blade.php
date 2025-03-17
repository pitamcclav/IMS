<!-- resources/views/user-management.blade.php -->

@extends('layouts.app')

@section('title', 'User Management')
@section('content')
    <div class="container-fluid" x-data="roleManager()">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">User Management</h1>
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Add New User
            </a>
        </div>
        
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg font-medium text-gray-900">User List</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($staff as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->staffId }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->staffName }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($user->roles->isNotEmpty())
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <button type="button" 
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            @click="openModal('{{ $user->staffId }}', '{{ $user->staffName }}')">
                                            Assign
                                        </button>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('users.edit', $user->staffId) }}" 
                                        class="inline-flex items-center p-1.5 border border-transparent rounded-md text-yellow-600 hover:text-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                        <i class="lni lni-pencil"></i>
                                    </a>
                                    {{-- <button class="inline-flex items-center p-1.5 border border-transparent rounded-md text-red-600 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" 
                                            @click.prevent="$dispatch('open-delete-modal', {url: '{{ route('users.destroy', $user->staffId) }}'})">
                                        <i class="lni lni-trash-can"></i>
                                    </button> --}}
                                    <button type="button"
                                                    class="text-red-600 hover:text-red-900"
                                                    @click="window.dispatchEvent(new CustomEvent('open-delete-modal', { 
                                                        detail: {
                                                            id: '{{ $user->staffId }}', 
                                                            url: '{{ route('users.destroy', $user->staffId) }}', 
                                                            redirect: window.location.href
                                                        }
                                                    }))">
                                                <i class="lni lni-trash-can"></i>
                                            </button>
                                    @if($user->roles->isNotEmpty())
                                        <form action="{{ route('roles.revoke', $user->staffId) }}" method="POST" class="inline">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                                @click="return confirm('Are you sure you want to revoke roles for this user?')">
                                                Revoke
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Assign Role Modal -->
        <div x-show="showModal" 
            class="fixed inset-0 z-10 overflow-y-auto" 
            aria-labelledby="modal-title" 
            role="dialog" 
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="closeModal"
                    aria-hidden="true"></div>

                <!-- Modal panel -->
                <div x-show="showModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Assign Role to <span x-text="selectedUser.name"></span>
                            </h3>
                            <div class="mt-4">
                                <form id="rolesForm" @submit.prevent="assignRole($event)">
                                    <input type="hidden" name="staffId" :value="selectedUser.id">
                                    <div class="space-y-4">
                                        <label class="text-sm font-medium text-gray-700">Select Role</label>
                                        @foreach($roles as $role)
                                            <div class="flex items-center">
                                                <input type="radio" name="role" 
                                                    value="{{ $role->name }}" 
                                                    id="role{{ $role->id }}" 
                                                    required
                                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                                <label for="role{{ $role->id }}" class="ml-3 block text-sm font-medium text-gray-700">
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                        <button type="submit" 
                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                                            :disabled="loading">
                                            <span x-show="loading" class="inline-block animate-spin h-4 w-4 mr-2 border-2 border-white border-t-transparent rounded-full"></span>
                                            Assign Role
                                        </button>
                                        <button type="button" 
                                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                                            @click="closeModal">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.modals.delete-modal')

        <!-- Loading overlay -->
        <div x-show="loading" 
            class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-white border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" 
                role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{asset('js/admin.js')}}"></script>
@endsection
