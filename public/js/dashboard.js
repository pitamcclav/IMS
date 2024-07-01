document.addEventListener('DOMContentLoaded', () => {
    "use strict";

    // Log inventory data content for debugging
    console.log(document.getElementById('inventoryData').textContent);
    console.log(document.getElementById('pendingRequestsCount').textContent);

    // Data for Inventory Chart
    try {
        var inventoryData = JSON.parse(document.getElementById('inventoryData').textContent);
    } catch (e) {
        console.error('Error parsing inventoryData JSON:', e);
    }

    var inventoryLabels = inventoryData.map(function(item) {
        return 'Item ' + item.itemId;
    });
    var inventoryQuantities = inventoryData.map(function(item) {
        return item.quantity;
    });

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
            responsive: true
        }
    });
});
