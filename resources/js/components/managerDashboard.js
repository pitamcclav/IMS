import { Chart } from 'chart.js/auto';

export default () => ({
    inventoryChart: null,
    requestsChart: null,

    init() {
        // Destroy any existing charts first
        if (this.inventoryChart) {
            this.inventoryChart.destroy();
        }
        if (this.requestsChart) {
            this.requestsChart.destroy();
        }

        this.initializeInventoryChart();
        this.initializeRequestsChart();
        
        // Return cleanup function
        return () => {
            if (this.inventoryChart) {
                this.inventoryChart.destroy();
            }
            if (this.requestsChart) {
                this.requestsChart.destroy();
            }
        }
    },

    initializeInventoryChart() {
        const inventoryData = JSON.parse(document.getElementById('inventoryData').textContent);
        const ctx = document.getElementById('inventoryChart');

        if (!ctx) return;

        // Ensure any existing chart is destroyed
        if (this.inventoryChart) {
            this.inventoryChart.destroy();
        }

        this.inventoryChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: inventoryData.map(item => item.itemName),
                datasets: [{
                    data: inventoryData.map(item => item.quantity),
                    backgroundColor: [
                        '#4F46E5', // indigo-600
                        '#06B6D4', // cyan-500
                        '#10B981', // emerald-500
                        '#F59E0B', // amber-500
                        '#EF4444', // red-500
                        '#8B5CF6', // purple-500
                        '#EC4899', // pink-500
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                return `${label}: ${value} items`;
                            }
                        }
                    }
                }
            }
        });
    },

    initializeRequestsChart() {
        const pendingCount = parseInt(document.getElementById('pendingRequestsCount').textContent);
        const completedCount = parseInt(document.getElementById('completedRequestsCount')?.textContent || '0');
        const ctx = document.getElementById('requestsChart');

        if (!ctx) return;

        // Ensure any existing chart is destroyed
        if (this.requestsChart) {
            this.requestsChart.destroy();
        }

        this.requestsChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Pending Requests', 'Completed Requests'],
                datasets: [{
                    data: [pendingCount, completedCount],
                    backgroundColor: [
                        '#F59E0B', // amber-500 for pending
                        '#10B981', // emerald-500 for completed
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                return `${label}: ${value}`;
                            }
                        }
                    }
                }
            }
        });
    }
});