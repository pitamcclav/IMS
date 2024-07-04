document.addEventListener('DOMContentLoaded', () => {
    "use strict";

    const addItemBtn = document.getElementById('addItemBtn');
    const requestDetailsContainer = document.getElementById('requestDetailsContainer');
    const itemOptions = document.getElementById('itemOptions').innerHTML;
    const colourOptions = document.getElementById('colourOptions').innerHTML;
    const sizeOptions = document.getElementById('sizeOptions').innerHTML;

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
                <select name="sizeIds[]" class="form-control">
                    ${sizeOptions}
                </select>
            </td>
            <td>
                <select name="colourIds[]" class="form-control">
                    ${colourOptions}
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

        attachEventListeners(row);
        updateAddRowButtons();
    }

    // Attach event listeners to the row elements
    function attachEventListeners(row) {
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
                            <th>Size</th>
                            <th>Colour</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="variant-row">
                            <td>
                                <select name="sizeIds[]" class="form-control">
                                    ${sizeOptions}
                                </select>
                            </td>
                            <td>
                                <select name="colourIds[]" class="form-control">
                                    ${colourOptions}
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
