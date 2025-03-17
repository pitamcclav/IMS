import { Chart } from 'chart.js/auto';

export default () => ({
    usersChart: null,
    itemsChart: null,
    
    init() {
        // Destroy any existing charts first
        if (this.usersChart) {
            this.usersChart.destroy();
        }
        if (this.itemsChart) {
            this.itemsChart.destroy();
        }

        this.initUsersChart();
        this.initItemsChart();
    },
    
    initUsersChart() {
        try {
            const usersDataElement = document.getElementById('usersData');
            if (!usersDataElement) return;
            
            const usersData = JSON.parse(usersDataElement.textContent);
            const usersChartData = [usersData.activeUsers, usersData.inactiveUsers];
            
            const ctxUsers = document.getElementById('usersChart').getContext('2d');
            this.usersChart = new Chart(ctxUsers, {
                type: 'pie',
                data: {
                    labels: ['Active Users', 'Inactive Users'],
                    datasets: [{
                        data: usersChartData,
                        backgroundColor: [
                            '#10B981', // emerald-500 for active
                            '#F59E0B'  // amber-500 for inactive
                        ]
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
        } catch (error) {
            console.error('Error initializing users chart:', error);
        }
    },
    
    initItemsChart() {
        try {
            const itemsDataElement = document.getElementById('itemsData');
            if (!itemsDataElement) return;
            
            const itemsData = JSON.parse(itemsDataElement.textContent);
            const itemsLabels = itemsData.map(item => item.itemName);
            const itemsQuantities = itemsData.map(item => item.quantity);
            
            const ctxItems = document.getElementById('itemsChart').getContext('2d');
            this.itemsChart = new Chart(ctxItems, {
                type: 'pie',
                data: {
                    labels: itemsLabels,
                    datasets: [{
                        data: itemsQuantities,
                        backgroundColor: [
                            '#4F46E5', // indigo-600
                            '#06B6D4', // cyan-500
                            '#10B981', // emerald-500
                            '#F59E0B', // amber-500
                            '#EF4444', // red-500
                            '#8B5CF6', // purple-500
                            '#EC4899', // pink-500
                        ]
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
        } catch (error) {
            console.error('Error initializing items chart:', error);
        }
    },
    
    destroy() {
        if (this.usersChart) {
            this.usersChart.destroy();
        }
        if (this.itemsChart) {
            this.itemsChart.destroy();
        }
    }
});