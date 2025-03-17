// Main app.js file (first part remains largely the same)
import Alpine from 'alpinejs';
import $ from 'jquery';

// Import FilePond and plugins
import * as FilePond from 'filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImageValidateSize from 'filepond-plugin-image-validate-size';

// Import FilePond styles
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';

// Import components
import deleteModal from './components/deleteModal';
import inventoryManager from './components/inventoryManager';
import requestManager from './components/requestManager';
import supplierManager from './components/supplierManager';
import adminDashboard from './components/adminDashboard';
import roleManager from './components/roleManager';
import managerDashboard from './components/managerDashboard';
import sidebar from './components/sidebar';
import categoryManager from './components/categoryManager';

// Make dependencies available globally
window.Alpine = Alpine;
window.$ = window.jQuery = $;
window.FilePond = FilePond;

// Register FilePond plugins globally
FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateType,
    FilePondPluginImageValidateSize
);

// Initialize Alpine stores
Alpine.store('notifications', {
    items: [],
    add(notification) {
        const id = Date.now();
        this.items.push({ id, ...notification });
        setTimeout(() => this.remove(id), 5000);
    },
    remove(id) {
        this.items = this.items.filter(item => item.id !== id);
    }
});

Alpine.store('sidebar', {
    isOpen: window.innerWidth >= 768,
    toggle() { this.isOpen = !this.isOpen; },
    open() { this.isOpen = true; },
    close() { this.isOpen = false; }
});

Alpine.store('modals', {
    items: {},
    open(modalId) { this.items[modalId] = true; },
    close(modalId) { this.items[modalId] = false; },
    toggle(modalId) { this.items[modalId] = !this.items[modalId]; },
    isOpen(modalId) { return !!this.items[modalId]; }
});

// Register Alpine components
Alpine.data('deleteModal', deleteModal);
Alpine.data('inventoryManager', inventoryManager);
Alpine.data('requestManager', requestManager);
Alpine.data('supplierManager', supplierManager);
Alpine.data('adminDashboard', adminDashboard);
Alpine.data('roleManager', roleManager);
Alpine.data('managerDashboard', managerDashboard);
Alpine.data('sidebar', sidebar);
Alpine.data('categoryManager', categoryManager);

// Start Alpine
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});