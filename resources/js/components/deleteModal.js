export default () => ({
    showModal: false,
    itemId: null,
    deleteUrl: null,
    redirectUrl: null,

    init() {
        // Using $nextTick to ensure the component is fully initialized before adding event listener
        this.$nextTick(() => {
            window.addEventListener('open-delete-modal', (e) => {
                this.openModal(e.detail);
            });
        });
    },

    openModal(detail) {
        // Check if the parameter is an object (coming from the event dispatch)
        if (typeof detail === 'object') {
            this.itemId = detail.id || null;
            this.deleteUrl = detail.url || null;
            this.redirectUrl = detail.redirect || null;
        } else {
            // For backward compatibility
            this.itemId = arguments[0];
            this.deleteUrl = arguments[1];
            this.redirectUrl = arguments[2] || null;
        }
        this.showModal = true;
    },

    closeModal() {
        this.showModal = false;
        this.itemId = null;
        this.deleteUrl = null;
        this.redirectUrl = null;
    },

    async confirmDelete() {
        try {
            const response = await fetch(this.deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const result = await response.json();
            
            // Show success notification
            Alpine.store('notifications').add({
                type: 'success',
                message: result.message || 'Item deleted successfully'
            });

            // Close the modal
            this.closeModal();

            // Redirect if URL provided, otherwise refresh the page
            if (this.redirectUrl) {
                window.location.href = this.redirectUrl;
            } else {
                window.location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
            Alpine.store('notifications').add({
                type: 'error',
                message: 'An error occurred while deleting the item'
            });
        }
    }
});