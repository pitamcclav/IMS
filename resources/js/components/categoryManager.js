export default () => ({
    selectedCategory: null,
    isFormModalOpen: false,
    editingCategory: null,
    formData: {
        categoryName: '',
        isReturnable: false
    },
    
    init() {
        // Setup delete modal event listener
        document.addEventListener('open-delete-modal', (e) => {
            const deleteModal = Alpine.evaluate(document.querySelector('[x-data="deleteModal()"]'), 'openModal');
            deleteModal(e.detail.url);
        });
    },

    openCreateModal() {
        this.editingCategory = null;
        this.formData = {
            categoryName: '',
            isReturnable: false
        };
        this.isFormModalOpen = true;
    },

    openEditModal(categoryData) {
        const category = typeof categoryData === 'string' ? JSON.parse(categoryData) : categoryData;
        this.editingCategory = category;
        this.formData = {
            categoryName: category.categoryName,
            isReturnable: category.isReturnable
        };
        this.isFormModalOpen = true;
    },

    async submitForm() {
        const url = this.editingCategory 
            ? `/category/${this.editingCategory.categoryId}`
            : '/category';
        
        const method = this.editingCategory ? 'PUT' : 'POST';

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
                throw new Error(error.message || 'Failed to save category');
            }
        } catch (error) {
            console.error('Error:', error);
            if (window.Alpine && window.Alpine.store('notifications')) {
                window.Alpine.store('notifications').add({
                    type: 'error',
                    message: error.message || 'Failed to save category'
                });
            }
        }
    },

    closeFormModal() {
        this.isFormModalOpen = false;
        this.editingCategory = null;
        this.formData = {
            categoryName: '',
            isReturnable: false
        };
    }
});