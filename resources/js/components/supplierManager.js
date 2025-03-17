export default () => ({
    selectedSupplier: null,
    isViewModalOpen: false,
    isFormModalOpen: false,
    editingSupplier: null,
    formData: {
        supplierName: '',
        contactInfo: ''
    },
    
    init() {
        // Setup delete modal event listener
        document.addEventListener('open-delete-modal', (e) => {
            const deleteModal = Alpine.evaluate(document.querySelector('[x-data="deleteModal()"]'), 'openModal');
            deleteModal(e.detail.url);
        });
    },

    openCreateModal() {
        this.editingSupplier = null;
        this.formData = {
            supplierName: '',
            contactInfo: ''
        };
        this.isFormModalOpen = true;
    },

    openEditModal(supplierData) {
        const supplier = typeof supplierData === 'string' ? JSON.parse(supplierData) : supplierData;
        this.editingSupplier = supplier;
        this.formData = {
            supplierName: supplier.supplierName,
            contactInfo: supplier.contactInfo
        };
        this.isFormModalOpen = true;
    },

    async submitForm() {
        const url = this.editingSupplier 
            ? `/supplier/${this.editingSupplier.supplierId}`
            : '/supplier';
        
        const method = this.editingSupplier ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.formData)
            });

            if (response.ok) {
                window.location.reload();
            } else {
                const error = await response.json();
                throw new Error(error.message || 'Failed to save supplier');
            }
        } catch (error) {
            console.error('Error:', error);
            // If you have a notification system:
            if (window.Alpine && window.Alpine.store('notifications')) {
                window.Alpine.store('notifications').add({
                    type: 'error',
                    message: error.message || 'Failed to save supplier'
                });
            }
        }
    },

    closeFormModal() {
        this.isFormModalOpen = false;
        this.editingSupplier = null;
        this.formData = {
            supplierName: '',
            contactInfo: ''
        };
    },

    viewSupplierDetails(supplierData) {
        try {
            this.selectedSupplier = typeof supplierData === 'string' 
                ? JSON.parse(supplierData) 
                : supplierData;
            this.isViewModalOpen = true;
        } catch (error) {
            console.error('Error parsing supplier data:', error);
            if (window.Alpine && window.Alpine.store('notifications')) {
                window.Alpine.store('notifications').add({
                    type: 'error',
                    message: 'Error loading supplier details'
                });
            }
        }
    },
    
    closeViewModal() {
        this.isViewModalOpen = false;
        this.selectedSupplier = null;
    },
    
    getDeliveryNotes(notesJson) {
        try {
            return notesJson ? JSON.parse(notesJson) : [];
        } catch (error) {
            console.error('Error parsing delivery notes:', error);
            return [];
        }
    }
});