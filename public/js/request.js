document.addEventListener('DOMContentLoaded', () => {
    "use strict";

    const addItemBtn = document.getElementById('addItemBtn');
    const requestDetailsContainer = document.getElementById('requestDetailsContainer');
    const itemOptions = document.getElementById('itemOptions').innerHTML;
    const colourOptions = document.getElementById('colourOptions').innerHTML;
    const sizeOptions = document.getElementById('sizeOptions').innerHTML;

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
        const detailDiv = document.createElement('div');
        detailDiv.classList.add('requestDetail', 'mb-3');
        detailDiv.innerHTML = `

            <label for="item">Item</label>
            <select name="itemIds[]" class="form-control item-select mb-2">
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
                        <tr class="variant-row">
                            <td>
                                <select name="colourIds[]" class="form-control colour-select">
                                    ${colourOptions}
                                </select>
                            </td>
                            <td>
                                <select name="sizeIds[]" class="form-control size-select">
                                    ${sizeOptions}
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


        attachEventListeners(detailDiv);
        updateAddRowButtons();
    }

    addItemBtn.addEventListener('click', addRequestDetail);

    // Initialize event listeners for the initial rows
    document.querySelectorAll('.variant-row').forEach(row => {
        attachEventListeners(row);
    });

    updateAddRowButtons();
});
