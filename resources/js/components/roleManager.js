export default () => ({
    loading: false,
    showModal: false,
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
    selectedUser: {
        id: null,
        name: ''
    },
    
    openModal(userId, userName) {
        this.selectedUser = {
            id: userId,
            name: userName
        };
        this.showModal = true;
    },
    
    closeModal() {
        this.showModal = false;
        this.selectedUser = {
            id: null,
            name: ''
        };
    },
    
    async assignRole(event) {
        event.preventDefault();
        this.loading = true;
        
        try {
            const form = event.target;
            const formData = new FormData(form);
            
            const response = await fetch('/assign-roles', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                Alpine.store('notifications').add({
                    type: 'success',
                    message: 'Role assigned successfully!'
                });
                
                this.closeModal();
                
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Failed to assign role');
            }
        } catch (error) {
            console.error('Error:', error);
            Alpine.store('notifications').add({
                type: 'error',
                message: 'An error occurred while assigning the role.'
            });
        } finally {
            this.loading = false;
        }
    }
});