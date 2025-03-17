@extends('layouts.app')

@section('title', 'Supplier Management')

@section('content')
    <div class="container-fluid" x-data="supplierManager()" x-init="init">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Supplier Management</h2>
                <p class="mt-2 text-sm text-gray-600">Manage suppliers and their supply records</p>
            </div>
            <button @click="openCreateModal()" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="lni lni-plus mr-2"></i>
                Add New Supplier
            </button>
        </div>

        <!-- Main Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($suppliers as $supplier)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $supplier->supplierId }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $supplier->supplierName }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $supplier->contactInfo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button class="text-blue-600 hover:text-blue-900" @click="viewSupplierDetails(@js($supplier))">
                                        <i class="lni lni-eye"></i>
                                    </button>
                                    <button class="text-yellow-600 hover:text-yellow-900" @click="openEditModal(@js($supplier))">
                                        <i class="lni lni-pencil"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900" 
                                            @click="$dispatch('open-delete-modal', {url: '{{ route('supplier.destroy', $supplier->supplierId) }}'})">
                                        <i class="lni lni-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- View Details Modal -->
        <div x-show="isViewModalOpen" class="fixed inset-0 overflow-y-auto z-50" x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="isViewModalOpen" 
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="closeViewModal">
                </div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Supplier Details
                                </h3>
                                <div class="mt-4 space-y-4" x-show="selectedSupplier">
                                    <div>
                                        <p class="text-sm text-gray-500">ID</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedSupplier.supplierId"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Name</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedSupplier.supplierName"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Contact</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedSupplier.contactInfo"></p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">Supply Details</h4>
                                        <div class="mt-2">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supply Date</th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Note</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    <template x-for="supply in selectedSupplier.supply" :key="supply.supplyId">
                                                        <tr>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="supply.item ? supply.item.itemName : 'N/A'"></td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="supply.quantity"></td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="supply.supplyDate"></td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                <template x-if="supply.delivery_notes">
                                                                    <template x-for="note in getDeliveryNotes(supply.delivery_notes)" :key="note.id">
                                                                        <a :href="'/storage/' + note.path" 
                                                                           class="text-blue-600 hover:text-blue-900" 
                                                                           target="_blank" 
                                                                           x-text="note.original_name"></a>
                                                                    </template>
                                                                </template>
                                                                <template x-if="!supply.delivery_notes">
                                                                    <span>N/A</span>
                                                                </template>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="closeViewModal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div x-show="isFormModalOpen" class="fixed inset-0 overflow-y-auto z-50" x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="closeFormModal">
                </div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitForm">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" x-text="editingSupplier ? 'Edit Supplier' : 'Add New Supplier'"></h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="supplierName" class="block text-sm font-medium text-gray-700">Supplier Name</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="lni lni-user text-gray-400"></i>
                                        </div>
                                        <input type="text" 
                                               id="supplierName" 
                                               x-model="formData.supplierName"
                                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                               placeholder="Enter supplier name"
                                               required>
                                    </div>
                                </div>

                                <div>
                                    <label for="contactInfo" class="block text-sm font-medium text-gray-700">Contact Information</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="lni lni-phone text-gray-400"></i>
                                        </div>
                                        <input type="text" 
                                               id="contactInfo" 
                                               x-model="formData.contactInfo"
                                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                               placeholder="Enter contact information"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                <i class="lni lni-save mr-2"></i>
                                Save
                            </button>
                            <button type="button" 
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                    @click="closeFormModal">
                                <i class="lni lni-close mr-2"></i>
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @include('partials.modals.delete-modal')
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </div>
@endsection

