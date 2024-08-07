document.addEventListener('DOMContentLoaded', function () {
    "use strict"

    // Handle view button click
    document.querySelectorAll('.view-btn').forEach(function (button) {
        button.addEventListener('click', function () {


            // Get supplier data from button
            const supplierData = this.getAttribute('data-supplier');


            if (supplierData) {
                const supplier = JSON.parse(supplierData);

                // Debug: Log the supplier data to the console
                console.log(supplier);

                // Populate modal with supplier details
                document.getElementById('supplierId').textContent = supplier.supplierId;
                document.getElementById('supplierName').textContent = supplier.supplierName;
                document.getElementById('supplierContact').textContent = supplier.contactInfo;

                // Populate supply details
                const supplyDetailsElement = document.getElementById('supplyDetails');
                supplyDetailsElement.innerHTML = ''; // Clear previous details

                supplier.supply.forEach(function (detail) {
                    // Debug: Log each supply detail to the console

                    // Parse the delivery_notes JSON string
                    let deliveryNotes = [];
                    if (detail.delivery_notes) {
                        deliveryNotes = JSON.parse(detail.delivery_notes);
                    }

                    const row = document.createElement('tr');
                    row.innerHTML = `
                                <td>${detail.item ? detail.item.itemName : 'N/A'}</td>
                                <td>${detail.quantity}</td>
                                <td>${detail.supplyDate}</td>
                                <td>
                            ${deliveryNotes.length > 0 ? deliveryNotes.map(note =>
                        `<a href="/storage/${note.path}" target="_blank">${note.original_name}</a>`).join(', ') : 'N/A'}
                        </td>

                            `;
                    supplyDetailsElement.appendChild(row);
                });
            } else {
                console.log("No supplier data found on button");
            }
        });
    });
});
