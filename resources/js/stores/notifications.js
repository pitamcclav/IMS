export default {
    items: [],
    add(notification) {
        const items = this.items || [];
        items.push({
            id: Date.now(),
            type: notification.type,
            message: notification.message
        });
        this.items = items;
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            this.items = (this.items || []).filter(i => i.id !== notification.id);
        }, 3000);
    },
    remove(id) {
        this.items = (this.items || []).filter(i => i.id !== id);
    }
};