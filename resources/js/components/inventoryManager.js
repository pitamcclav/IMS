import Alpine from "alpinejs";

export default (initialSizes = [], initialColours = []) => ({
    // State properties
    loading: false,
    activeModal: null,
    pond: null,
    colorForm: {
        colorName: ''
    },
    sizeForm: {
        sizeValue: ''  // Changed from sizeName to sizeValue to match model
    },
    itemForm: {
        itemName: '',
        description: '',
        categoryId: ''
    },
    supplierForm: {
        supplierName: '',
        contactInfo: ''
    },

    init() {
        this.loading = false;
        this.activeModal = null;

        // Ensure DOM is fully loaded before initializing FilePond
        this.$nextTick(() => {
            if (window.FilePond && document.querySelector('#fileInput')) {
                this.initializeFilePond();
            } else {
                // Retry initialization if elements aren't ready
                setTimeout(() => this.initializeFilePond(), 100);
            }
        });
    },

    initializeFilePond() {
        const inputElement = document.querySelector('#fileInput');
        if (!inputElement) {
            console.error('File input element not found');
            return;
        }

        const apiToken = document.querySelector('meta[name="api-token"]')?.content;
        console.log('API Token from meta tag:', apiToken);
        if (!apiToken) {
            console.error('API token not found in meta tag');
            Alpine.store('notifications').add({
                type: 'error',
                message: 'Please log in again to upload files'
            });
            
            setTimeout(() => window.location.href = '/', 1000);
            return;
        }
        

        try {
            // Destroy existing instance if it exists
            if (this.pond) {
                this.pond.destroy();
            }

            this.pond = window.FilePond.create(inputElement, {
                name       : 'image',
                labelIdle: `<span class="filepond--label-action">Browse</span> or drag and drop your images`,
                imagePreviewHeight: 170,
                styleLoadIndicatorPosition: 'center bottom',
                styleProgressIndicatorPosition: 'right bottom',
                styleButtonRemoveItemPosition: 'left bottom',
                allowMultiple: true,
                maxFiles: 5,
                maxFileSize: '3MB',
                acceptedFileTypes: ['image/*'],
                server: {
                    url: '/api',
                    process: {
                        url: '/upload',
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${apiToken}`,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        withCredentials: true,
                        onload: (response) => {
                            console.log('File uploaded:', response);
                            try {
                                const res = JSON.parse(response);
                                if (!res.success) {
                                    throw new Error(res.message || 'Upload failed');
                                }
                                if (!res.serverId) {
                                    throw new Error('Server response missing serverId');
                                }
                                return res.serverId; // Return the serverId for FilePond to use
                            } catch (e) {
                                console.error('Error parsing response:', e);
                                throw new Error('Invalid server response: ' + e.message); // Trigger FilePond error handling
                            }
                        },
                        onerror: (response) => {
                            console.error('Error uploading file:', response);
                            return response.data || response;
                        }
                    }
                },
                oninit: () => {
                    console.log('FilePond initialized successfully');
                },
                onaddfile: (error, fileItem) => {
                    if (error) {
                        Alpine.store('notifications').add({
                            type: 'error',
                            message: `Failed to add file: ${error.main}`
                        });
                    }
                },
                onprocessfile: (error, fileItem) => {
                    if (error) {
                        Alpine.store('notifications').add({
                            type: 'error',
                            message: `Upload failed: ${error.main}`
                        });
                    }else{
                        console.log('File uploaded successfully:', fileItem.file.name);
                        Alpine.store('notifications').add({
                            type: 'success',
                            message: 'File uploaded successfully'
                        });
                    }
                }
            });

        } catch (error) {
            console.error('Error initializing FilePond:', error);
            Alpine.store('notifications').add({
                type: 'error',
                message: 'Failed to initialize file upload component'
            });
        }
    },

    // Simplified handleFileUpload (no longer needed with server config)
    handleFormSubmit(e) {
        e.preventDefault();
        
        if (!this.pond) {
            Alpine.store('notifications').add({
                type: 'error',
                message: 'File upload component not initialized'
            });
            return;
        }

        const files = this.pond.getFiles();
        if (!files.length) {
            Alpine.store('notifications').add({
                type: 'error',
                message: 'Please upload at least one image'
            });
            return;
        }

        const pendingFiles = files.filter(file => 
            file.status !== FilePond.FileStatus.PROCESSING_COMPLETE
        );
        if (pendingFiles.length > 0) {
            Alpine.store('notifications').add({
                type: 'warning',
                message: 'Please wait for all files to finish uploading'
            });
            return;
        }

        const form = e.target;
        const uploadedFiles = files
            .filter(file => file.serverId)
            .map(file => file.serverId);

            console.log('Form submitted with uploaded files:', uploadedFiles);

            form.querySelectorAll('input[name="uploadedFiles[]"]').forEach(input => input.remove());
        uploadedFiles.forEach((fileId) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'uploadedFiles[]';
            input.value = fileId;
            form.appendChild(input);
        });


        // Submit the form via fetch or regular submission
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Alpine.store('notifications').add({
                    type: 'success',
                    message: 'Inventory saved successfully'
                });
                
                if (data.redirect) {
                    setTimeout(() => window.location.href = data.redirect, 1000);
                }
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Alpine.store('notifications').add({
                type: 'error',
                message: error.message || 'Failed to save inventory'
            });
        });
    },
    
    // Handle select change for modals
    handleSelectChange(event) {
        if (event.target.value === 'new') {
            if (event.target.name === 'colourIds[]') {
                this.openModal('newColorModal');
            } else if (event.target.name === 'sizeIds[]') {
                this.openModal('newSizeModal');  
            } else if (event.target.id === 'itemid') {
                this.openModal('newItemModal');
            } else if (event.target.id === 'supplierid') {
                this.openModal('newSupplierModal');
            }
            event.target.value = '';
        }
    },

    // Modal methods
    openModal(modalName) {
        this.activeModal = modalName;
        document.body.classList.add('modal-open');
    },

    closeModal() {
        this.activeModal = null;
        this.loading = false;
        document.body.classList.remove('modal-open');
        this.resetForms();
    },

    resetForms() {
        this.colorForm.colorName = '';
        this.sizeForm.sizeValue = '';
        this.itemForm.itemName = '';
        this.itemForm.description = '';
        this.itemForm.categoryId = '';
        this.supplierForm.supplierName = '';
        this.supplierForm.contactInfo = '';
    },

    // Handle form submissions
    async submitForm(type) {
        try {
            this.loading = true;
            let url, data;

            switch (type) {
                case 'color':
                    url = '/api/colour';
                    data = { colorName: this.colorForm.colorName };
                    break;
                case 'size':
                    url = '/api/size';
                    data = { sizeValue: this.sizeForm.sizeValue };
                    break;
                case 'item':
                    url = '/api/item';
                    data = this.itemForm;
                    break;
                case 'supplier':
                    url = '/api/supplier';
                    data = this.supplierForm;
                    break;
            }

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `Failed to add ${type}`);
            }

            if (result.success) {
                this.updateSelectOptions(type, result);
                this.resetForms();
                this.closeModal();
                Alpine.store('notifications').add({
                    type: 'success',
                    message: `The ${type} was added successfully.`
                });
            } else {
                throw new Error(result.message || `Failed to add ${type}`);
            }
        } catch (error) {
            console.error('Error:', error);
            Alpine.store('notifications').add({
                type: 'error',
                message: error.message || 'An unexpected error occurred'
            });
        } finally {
            this.loading = false;
        }
    },

    // Update select options after adding new items
    updateSelectOptions(type, result) {
        let select, value, text;
        switch (type) {
            case 'color':
                select = 'colourIds\\[\\]';
                value = result.colour.colourId;
                text = result.colour.colourName;
                break;
            case 'size':
                select = 'sizeIds\\[\\]';
                value = result.size.sizeId;
                text = result.size.sizeValue;
                break;
            case 'item':
                select = 'itemid';
                value = result.item.itemId;
                text = result.item.itemName;
                break;
            case 'supplier':
                select = 'supplierid';
                value = result.supplier.supplierId;
                text = result.supplier.supplierName;
                break;
        }

        const selects = document.querySelectorAll(`[name="${select}"]${select === 'itemid' || select === 'supplierid' ? `, #${select}` : ''}`);
        selects.forEach(select => {
            const option = new Option(text, value);
            select.add(option, select.options[select.options.length - 1]);
            select.value = value;
        });
    },

    // Handle variant rows
    addInventoryRow() {
        const tbody = document.querySelector('tbody');
        const template = document.createElement('tr');
        template.classList.add('variant-row');
        template.innerHTML = `
            <td class="px-6 py-4">
                <select name="sizeIds[]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required @change="handleSelectChange($event)">
                    <option value="" disabled selected>Select Size</option>
                    ${Array.from(document.querySelector('select[name="sizeIds[]"]').options)
                        .filter(opt => opt.value !== 'new')
                        .map(opt => `<option value="${opt.value}">${opt.text}</option>`)
                        .join('')}
                    <option value="new">New Size</option>
                </select>
            </td>
            <td class="px-6 py-4">
                <select name="colourIds[]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required @change="handleSelectChange($event)">
                    <option value="" disabled selected>Select Colour</option>
                    ${Array.from(document.querySelector('select[name="colourIds[]"]').options)
                        .filter(opt => opt.value !== 'new')
                        .map(opt => `<option value="${opt.value}">${opt.text}</option>`)
                        .join('')}
                    <option value="new">New Colour</option>
                </select>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center space-x-2">
                    <button type="button" class="inline-flex items-center p-1 border border-transparent rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" @click="decrementQuantity($event)">
                        <svg class="h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>
                    <input type="number" name="quantities[]" class="block w-20 px-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md text-center" value="1" min="1">
                    <button type="button" class="inline-flex items-center p-1 border border-transparent rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" @click="incrementQuantity($event)">
                        <svg class="h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </button>
                </div>
            </td>
            <td class="px-6 py-4">
                <button type="button" class="text-red-600 hover:text-red-900" @click="removeRow($event)">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </td>
        `;
        tbody.appendChild(template);
    },

    decrementQuantity(event) {
        const input = event.target.closest('td').querySelector('input[type="number"]');
        if (input.value > 1) {
            input.value = parseInt(input.value) - 1;
        }
    },

    incrementQuantity(event) {
        const input = event.target.closest('td').querySelector('input[type="number"]');
        input.value = parseInt(input.value) + 1;
    },

    removeRow(event) {
        const rows = document.querySelectorAll('.variant-row');
        if (rows.length > 1) {
            event.target.closest('.variant-row').remove();
        } else {
            Alpine.store('notifications').add({
                type: 'warning',
                message: 'You must have at least one variant.'
            });
        }
    }
});