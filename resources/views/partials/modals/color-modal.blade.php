<!-- resources/views/partials/modals/color-modal.blade.php -->
<!-- Color Modal -->
<div x-show="activeModal === 'newColorModal'" 
    class="fixed inset-0 flex items-center justify-center z-50"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak>

    <!-- Modal backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeModal"></div>
    
    <!-- Modal content -->
    <div class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-md z-10 relative"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90">

        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h5 class="text-lg font-medium text-gray-900">Add New Color</h5>
                <button type="button" class="text-gray-400 hover:text-gray-500" @click="closeModal">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="px-6 py-4">
            <form @submit.prevent="submitForm('color')">
                <div class="mb-4">
                    <label for="colorName" class="block text-sm font-medium text-gray-700">Color Name</label>
                    <input type="text" id="colorName" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           x-model="colorForm.colorName" required>
                </div>
                <div class="mt-5">
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" 
                            :disabled="loading">
                        <span x-show="loading" class="mr-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        Save Colour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>