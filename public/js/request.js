document.addEventListener('DOMContentLoaded', () => {
    "use strict";

    // const Swal = require('sweetalert2');
    const addItemBtn = document.getElementById('addItemBtn');
    const requestDetailsContainer = document.getElementById('requestDetailsContainer');
    const itemOptions = document.getElementById('itemOptions').innerHTML;
    const colourOptions = document.getElementById('colourOptions').innerHTML;
    const sizeOptions = document.getElementById('sizeOptions').innerHTML;

    // Function to fetch items
    function fetchItems(storeId, callback) {
        fetch(`/fetch-items/${storeId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => callback(data))
            .catch(error => console.error('Error fetching items:', error));
    }

    // Function to fetch colours based on selected item
    function fetchColours(itemId, callback) {
        fetch(`/fetch-colours/${itemId}`)
            .then(response => response.json())
            .then(data => callback(data))
            .catch(error => console.error('Error fetching colours:', error));
    }

    // Function to fetch sizes based on selected item and colour
    function fetchSizes(itemId, colourId, callback) {
        fetch(`/fetch-sizes/${itemId}/${colourId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => callback(data))
            .catch(error => console.error('Error fetching sizes:', error));
    }

    // Add event listener to store dropdown
    // Add event listener to store dropdown
    document.getElementById('store').addEventListener('change', function() {
        const storeId = this.value;
        const itemSelect = document.getElementById('item');
        const colourSelect = document.getElementById('colour');
        const sizeSelect = document.getElementById('size');

        // Fetch items based on store selection
        fetchItems(storeId, items => {
            itemSelect.innerHTML = '<option value="" disabled selected>Select Item</option>';

            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.itemId;
                option.textContent = item.itemName;
                itemSelect.appendChild(option);
            });

            // Reset colour and size dropdowns
            colourSelect.innerHTML = '<option value="" disabled selected>Select Colour</option>';
            sizeSelect.innerHTML = '<option value="" disabled selected>Select Size</option>';
        });
    });

    // Add event listener to item dropdowns
    document.querySelectorAll('.item-select').forEach(itemSelect => {
        itemSelect.addEventListener('change', function() {
            const itemId = this.value;
            const variantContainer = this.closest('.requestDetail').querySelector('.item-variants');

            // Fetch colours and update dropdown
            fetchColours(itemId, colours => {
                const colourSelect = variantContainer.querySelector('.colour-select');
                colourSelect.innerHTML = '<option value="" disabled selected>Select Colour</option>';

                const colourArray = Object.values(colours);

                colourArray.forEach(colour => {
                    const option = document.createElement('option');
                    option.value = colour.colourId;
                    option.textContent = colour.colourName;
                    colourSelect.appendChild(option);
                });

                // Trigger change event on colour dropdown (if necessary)
                colourSelect.dispatchEvent(new Event('change'));
            });
        });
    });

// Add event listener to colour dropdowns
    document.querySelectorAll('.colour-select').forEach(colourSelect => {
        colourSelect.addEventListener('change', function() {
            const itemId = this.closest('.requestDetail').querySelector('.item-select').value;
            const colourId = this.value;
            const variantContainer = this.closest('.item-variants');

            // Fetch sizes and update dropdown
            fetchSizes(itemId, colourId, sizes => {
                console.log(sizes); // Ensure sizes are logged correctly
                const sizeSelect = variantContainer.querySelector('.size-select');
                sizeSelect.innerHTML = '<option value="" disabled selected>Select Size</option>';

                sizes.forEach(size => {
                    const option = document.createElement('option');
                    option.value = size.sizeId;
                    option.textContent = size.sizeValue;
                    sizeSelect.appendChild(option);
                });
            });
        });
    });


    // Update the visibility of the add row buttons
    function updateAddRowButtons() {
        document.querySelectorAll('.add-row-btn').forEach((btn, index, array) => {
            btn.style.display = (index === array.length - 1) ? 'inline-block' : 'none';
        });
    }

    // Add a new row to the variants table
    function addVariantRow(variantContainer) {
        const row = document.createElement('tr');
        row.classList.add('variant-row');
        row.innerHTML = `
         <td>
            <select name="colourIds[]" class="form-control colour-select">
                <option value="" disabled selected>Select Colour</option>
            </select>
        </td>
        <td>
            <select name="sizeIds[]" class="form-control size-select">
                <option value="" disabled selected>Select Size</option>
            </select>
        </td>
        <td>
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-outline-secondary minus-quantity">-</button>
                <input type="number" name="quantities[]" class="form-control mx-2 text-center" value="1" min="1">
                <button type="button" class="btn btn-outline-secondary plus-quantity">+</button>
            </div>
        </td>
        <td>
            <button type="button" class="btn btn-success add-row-btn">+</button>
            <button type="button" class="btn btn-danger remove-row-btn">-</button>
        </td>
    `;
        variantContainer.querySelector('tbody').appendChild(row);

        // Fetch colours for the newly added row
        const itemSelect = variantContainer.closest('.requestDetail').querySelector('.item-select');
        const itemId = itemSelect.value;
        const colourSelect = row.querySelector('.colour-select');

        fetchColours(itemId, colours => {
            colourSelect.innerHTML = '<option value="" disabled selected>Select Colour</option>';
            const colourArray = Object.values(colours);
            colourArray.forEach(colour => {
                const option = document.createElement('option');
                option.value = colour.colourId;
                option.textContent = colour.colourName;
                colourSelect.appendChild(option);
            });
        });


        attachEventListeners(row);
        updateAddRowButtons();
    }


    // Attach event listeners to the row elements
    function attachEventListeners(row) {

        row.querySelector('.colour-select').addEventListener('change', function () {
            const itemId = row.closest('.requestDetail').querySelector('.item-select').value;
            const colourId = this.value;
            const sizeSelect = row.querySelector('.size-select');

            fetchSizes(itemId, colourId, sizes => {
                sizeSelect.innerHTML = '<option value="" disabled selected>Select Size</option>';
                sizes.forEach(size => {
                    const option = document.createElement('option');
                    option.value = size.sizeId;
                    option.textContent = size.sizeValue;
                    sizeSelect.appendChild(option);
                });
            });
        });
        row.querySelector('.minus-quantity').addEventListener('click', function () {
            const quantityInput = row.querySelector('input[name="quantities[]"]');
            quantityInput.value = Math.max(1, parseInt(quantityInput.value) - 1);
        });

        row.querySelector('.plus-quantity').addEventListener('click', function () {
            const quantityInput = row.querySelector('input[name="quantities[]"]');
            quantityInput.value = parseInt(quantityInput.value) + 1;
        });

        row.querySelector('.add-row-btn').addEventListener('click', function () {
            addVariantRow(row.closest('.item-variants'));
        });

        row.querySelector('.remove-row-btn').addEventListener('click', function () {
            row.remove();
            updateAddRowButtons();
        });

        const removeDetailBtn = row.closest('.requestDetail').querySelector('.removeDetail');
        if (removeDetailBtn) {
            removeDetailBtn.addEventListener('click', function () {
                row.closest('.requestDetail').remove();
                updateAddRowButtons();
            });
        }
    }

    function addRequestDetail() {

        const requestDetailElements = document.querySelectorAll('.requestDetail');
        const index = requestDetailElements.length; // Get the next available index

        const detailDiv = document.createElement('div');
        detailDiv.classList.add(`requestDetail`, `requestDetail-${index}`, 'mb-3');
        detailDiv.innerHTML = `
            <label for="item">Item</label>
            <select name="itemIds[${index}]" class="form-control item-select item-${index} mb-2">
                ${itemOptions}
            </select>

            <div class="item-variants">
                <label>Variants</label>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Colour</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="variant-row variant-row-${index}">
                            <td>
                                <select name="colourIds[${index}]" class="form-control colour-select colour-${index}">
                                    ${colourOptions}
                                </select>
                            </td>
                            <td>
                                <select name="sizeIds[${index}]" class="form-control size-select size-${index}">
                                    ${sizeOptions}
                                </select>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-secondary minus-quantity">-</button>
                                    <input type="number" name="quantities[]" class="form-control mx-2 text-center quantity-${index}" value="1" min="1">
                                    <button type="button" class="btn btn-outline-secondary plus-quantity">+</button>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-success add-row-btn">+</button>
                                <button type="button" class="btn btn-danger remove-row-btn">-</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-danger removeDetail">Remove</button>
            <hr>
        `;
        requestDetailsContainer.appendChild(detailDiv);

        // Attach event listeners to the newly added item-select
        const newItemSelect = detailDiv.querySelector('.item-select');
        newItemSelect.addEventListener('change', function() {
            const itemId = this.value;
            const variantContainer = this.closest('.requestDetail').querySelector('.item-variants');

            // Fetch colours and update dropdown
            fetchColours(itemId, colours => {
                const colourSelect = variantContainer.querySelector('.colour-select');
                colourSelect.innerHTML = '<option value="" disabled selected>Select Colour</option>';

                const colourArray = Object.values(colours);

                colourArray.forEach(colour => {
                    const option = document.createElement('option');
                    option.value = colour.colourId;
                    option.textContent = colour.colourName;
                    colourSelect.appendChild(option);
                });

                // Trigger change event on colour dropdown (if necessary)
                colourSelect.dispatchEvent(new Event('change'));
            });
        });

        // Attach event listeners to the newly added colour-select
        const newColourSelect = detailDiv.querySelector('.colour-select');
        newColourSelect.addEventListener('change', function() {
            const itemId = this.closest('.requestDetail').querySelector('.item-select').value;
            const colourId = this.value;
            const variantContainer = this.closest('.item-variants');

            // Fetch sizes and update dropdown
            fetchSizes(itemId, colourId, sizes => {
                const sizeSelect = variantContainer.querySelector('.size-select');
                sizeSelect.innerHTML = '<option value="" disabled selected>Select Size</option>';

                sizes.forEach(size => {
                    const option = document.createElement('option');
                    option.value = size.sizeId;
                    option.textContent = size.sizeValue;
                    sizeSelect.appendChild(option);
                });
            });
        });


        attachEventListeners(detailDiv.querySelector('.variant-row'));
        updateAddRowButtons();
    }

    addItemBtn.addEventListener('click', addRequestDetail);

    document.getElementById('submitBtn').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default form submission

        const requestDetails = document.querySelectorAll('.requestDetail');
        const organizedData = [];

        requestDetails.forEach((detail, detailIndex) => {
            const itemId = detail.querySelector('.item-select').value;
            const variants = detail.querySelectorAll('.variant-row');

            variants.forEach((variant, variantIndex) => {
                const colourId = variant.querySelector('.colour-select').value;
                const sizeId = variant.querySelector('.size-select').value;
                const quantity = variant.querySelector('input[name^="quantities"]').value;

                // Create an object for each variant and append it to the organizedData array
                organizedData.push({
                    itemId: itemId,
                    colourId: colourId,
                    sizeId: sizeId,
                    quantity: quantity
                });
            });
        });

        console.log(organizedData);

        var staffId = $('#staff').val() ? $('#staff').val() : null;
        var storeId = $('#store').val() ? $('#store').val() : null;
        console.log(staffId);
        console.log(storeId);
        // You can now send organizedData to the backend using AJAX or a form submission
        // Example with AJAX:
        $.ajax({
            url: '/requests',
            type: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // If using Laravel
            },
            data: JSON.stringify({ data: organizedData, staffId: staffId, storeId: storeId }),
            success: function(response) {
                // Handle the response from the server
                console.log('Success:', response);

                // Redirect based on the response
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Request Submitted!',
                        text: 'The request has been successfully submitted.',
                        confirmButtonText: 'Okay'
                    }).then(() => {
                        window.location.href = response.redirect_url;
                    });
                    } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'An error occurred.',
                        confirmButtonText: 'Okay'
                    });

                }
            },
            error: function(error) {
                console.error('Error:', error);
                alert('An error occurred.');
            }
        });
    });

    // Initialize event listeners for the initial rows
    document.querySelectorAll('.variant-row').forEach(row => {
        attachEventListeners(row);
    });

    updateAddRowButtons();
});
