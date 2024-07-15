document.addEventListener('DOMContentLoaded', () => {
    "use strict";

    // Log users data content for debugging
    console.log(document.getElementById('usersData').textContent);
    console.log(document.getElementById('itemsData').textContent);

    // Data for Users Chart
    try {
        var usersData = JSON.parse(document.getElementById('usersData').textContent);
    } catch (e) {
        console.error('Error parsing usersData JSON:', e);
    }

    var usersChartData = [
        usersData.activeUsers,
        usersData.inactiveUsers
    ];

    // Users Chart
    var ctxUsers = document.getElementById('usersChart').getContext('2d');
    new Chart(ctxUsers, {
        type: 'pie',
        data: {
            labels: ['Active Users', 'Inactive Users'],
            datasets: [{
                data: usersChartData,
                backgroundColor: ['#007bff', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Data for Items Chart
    try {
        var itemsData = JSON.parse(document.getElementById('itemsData').textContent);
    } catch (e) {
        console.error('Error parsing itemsData JSON:', e);
    }

    var itemsLabels = itemsData.map(function(item) {
        return item.itemName;
    });
    var itemsQuantities = itemsData.map(function(item) {
        return item.quantity;
    });

    // Items Chart
    var ctxItems = document.getElementById('itemsChart').getContext('2d');
    new Chart(ctxItems, {
        type: 'pie',
        data: {
            labels: itemsLabels,
            datasets: [{
                data: itemsQuantities,
                backgroundColor: ['#007bff', '#dc3545', '#ffc107', '#28a745', '#17a2b8']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
