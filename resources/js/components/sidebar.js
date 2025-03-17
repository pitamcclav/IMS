export default () => ({
    init() {
        // Watch sidebar state
        this.$watch('$store.sidebar.isOpen', (isOpen) => {
            document.body.style.overflow = isOpen && window.innerWidth < 768 ? 'hidden' : 'auto';
            localStorage.setItem('sidebarState', JSON.stringify(isOpen));
        });

        // Load saved state
        const savedState = localStorage.getItem('sidebarState');
        if (savedState !== null) {
            this.$store.sidebar.isOpen = JSON.parse(savedState);
        }

        // Handle resize
        window.addEventListener('resize', () => {
            const isMobile = window.innerWidth < 768;
            if (!isMobile && !this.$store.sidebar.isOpen) {
                this.$store.sidebar.open();
            }
        });
    }
});

