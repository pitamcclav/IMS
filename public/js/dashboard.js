document.addEventListener('DOMContentLoaded', () => {
    "use strict";


    // Data for Inventory Chart
    try {
        var inventoryData = JSON.parse(document.getElementById('inventoryData').textContent);

        console.log(inventoryData);
    } catch (e) {
        console.error('Error parsing inventoryData JSON:', e);
    }

    var inventoryLabels = inventoryData.map(function(item) {
        return  item.itemName;
    });
    var inventoryQuantities = inventoryData.map(function(item) {
        return item.quantity;
    });
    console.log(inventoryLabels);
    console.log(inventoryQuantities);

    // Inventory Chart
    var ctxInventory = document.getElementById('inventoryChart').getContext('2d');
    new Chart(ctxInventory, {
        type: 'pie',
        data: {
            labels: inventoryLabels,
            datasets: [{
                data: inventoryQuantities,
                backgroundColor: ['#007bff', '#dc3545', '#ffc107', '#28a745', '#17a2b8']
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true
        }
    });

    // Data for Pending Requests Chart
    try {
        var pendingRequestsCount = JSON.parse(document.getElementById('pendingRequestsCount').textContent);
    } catch (e) {
        console.error('Error parsing pendingRequestsCount JSON:', e);
    }

    var requestsChartData = [pendingRequestsCount, 100 - pendingRequestsCount];

    // Requests Chart
    var ctxRequests = document.getElementById('requestsChart').getContext('2d');
    new Chart(ctxRequests, {
        type: 'pie',
        data: {
            labels: ['Pending', 'Other'],
            datasets: [{
                data: requestsChartData,
                backgroundColor: ['#ffc107', '#6c757d']
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true
        }
    });
});
