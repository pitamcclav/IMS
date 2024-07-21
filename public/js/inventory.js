document.addEventListener('DOMContentLoaded', function () {
    "use strict";

    // Function to open the respective modal
    function openModal(modalId) {
        new bootstrap.Modal(document.getElementById(modalId)).show();
    }

    // Delegate event listener for 'New' option in select elements
    document.addEventListener('change', function (e) {
        if (e.target.value === 'new') {
            if (e.target.name === 'supplierid') {
                openModal('newSupplierModal');
            } else if (e.target.name === 'itemid') {
                openModal('newItemModal');
            } else if (e.target.name === 'sizeIds[]') {
                openModal('newSizeModal');
            } else if (e.target.name === 'colourIds[]') {
                openModal('newColorModal');
            }
        }
    });

    // Add client-side validation for quantity
    document.querySelectorAll('input[name="quantities[]"]').forEach(input => {
        input.addEventListener('input', function () {
            let quantity = this.value.trim();
            if (quantity !== '' && isNaN(quantity)) {
                this.setCustomValidity('Quantity must be a number');
            } else {
                this.setCustomValidity('');
            }
        });
    });

    // CSRF token setup for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Show loading spinner during AJAX requests
    $(document).ajaxStart(function () {
        $('#loadingSpinner').show();
    }).ajaxStop(function () {
        $('#loadingSpinner').hide();
    });

    function updateSelectOptions(selectName, newOption) {
        const selects = document.querySelectorAll(`select[name="${selectName}"]`);
        selects.forEach(select => {
            select.insertAdjacentHTML('beforeend', `<option value="${newOption.value}">${newOption.text}</option>`);
        });
    }

    // Handle form submissions for modals
    document.getElementById('newSupplierForm').addEventListener('submit', function (e) {
        e.preventDefault();
        let supplierName = document.getElementById('newSupplierName').value;
        let supplierContact = document.getElementById('newSupplierContact').value;

        $.ajax({
            url: '/supplier',
            type: 'POST',
            data: {
                supplierName: supplierName,
                contactInfo: supplierContact
            },
            success: function(response) {
                if (response.success) {
                    $('#supplierid').append(new Option(response.supplier.supplierName, response.supplier.supplierId));
                    $('#newSupplierModal').modal('hide');
                    document.getElementById('newSupplierName').value = '';
                    document.getElementById('newSupplierContact').value = '';
                    Swal.fire({
                        icon: 'success',
                        title: 'Supplier Added!',
                        text: 'The supplier was added successfully.',
                        confirmButtonText: 'Okay'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Add Supplier',
                        text: 'There was a problem adding the supplier. Please try again.',
                        confirmButtonText: 'Okay'
                    });
                }
            },
            error: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again later.',
                    confirmButtonText: 'Okay'
                });
            }
        });
    });

    document.getElementById('newItemForm').addEventListener('submit', function (e) {
        e.preventDefault();
        let itemName = document.getElementById('newItemName').value;
        let itemDescription = document.getElementById('newItemDescription').value;
        let categoryId = document.getElementById('newItemCategory').value;

        $.ajax({
            url: '/item',
            type: 'POST',
            data: {
                itemName: itemName,
                description: itemDescription,
                categoryId: categoryId
            },
            success: function(response) {
                if (response.success) {
                    $('#itemid').append(new Option(response.item.itemName, response.item.itemId));
                    $('#newItemModal').modal('hide');
                    document.getElementById('newItemName').value = '';
                    document.getElementById('newItemDescription').value = '';
                    document.getElementById('newItemCategory').value = '';
                    Swal.fire({
                        icon: 'success',
                        title: 'Item Added!',
                        text: 'The item was added successfully.',
                        confirmButtonText: 'Okay'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Add Item',
                        text: 'There was a problem adding the item. Please try again.',
                        confirmButtonText: 'Okay'
                    });
                }
            },
            error: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again later.',
                    confirmButtonText: 'Okay'
                });
            }
        });
    });

    document.getElementById('newColorForm').addEventListener('submit', function (e) {
        e.preventDefault();
        let colorName = document.getElementById('newColorName').value;

        $.ajax({
            url: '/api/colour',
            type: 'POST',
            data: {
                colorName: colorName
            },
            success: function(response) {
                if (response.success) {
                    updateSelectOptions('colourIds[]', { value: response.colour.colourId, text: response.colour.colourName });
                    $('#newColorModal').modal('hide');
                    document.getElementById('newColorName').value = '';
                    Swal.fire({
                        icon: 'success',
                        title: 'Color Added!',
                        text: 'The color was added successfully.',
                        confirmButtonText: 'Okay'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Add Color',
                        text: 'There was a problem adding the color. Please try again.',
                        confirmButtonText: 'Okay'
                    });
                }
            },
            error: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again later.',
                    confirmButtonText: 'Okay'
                });
            }
        });
    });

    document.getElementById('newSizeForm').addEventListener('submit', function (e) {
        e.preventDefault();
        let sizeValue = document.getElementById('newSizeValue').value;

        $.ajax({
            url: '/api/size',
            type: 'POST',
            data: {
                sizeValue: sizeValue
            },
            success: function(response) {
                if (response.success) {
                    updateSelectOptions('sizeIds[]', { value: response.size.sizeId, text: response.size.sizeValue });
                    $('#newSizeModal').modal('hide');
                    document.getElementById('newSizeValue').value = '';
                    Swal.fire({
                        icon: 'success',
                        title: 'Size Added!',
                        text: 'The size was added successfully.',
                        confirmButtonText: 'Okay'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Add Size',
                        text: 'There was a problem adding the size. Please try again.',
                        confirmButtonText: 'Okay'
                    });
                }
            },
            error: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again later.',
                    confirmButtonText: 'Okay'
                });
            }
        });
    });

    // Add row for new inventory details
    document.getElementById('addInventoryBtn').addEventListener('click', function () {
        let tbody = document.querySelector('tbody');
        let newRow = document.createElement('tr');
        newRow.classList.add('variant-row');

        newRow.innerHTML = `
        <td>
            <select name="sizeIds[]" class="form-control" required>
                <option value="" disabled selected>Select Size</option>
                ${sizes.map(size => `<option value="${size.sizeId}">${size.sizeValue}</option>`).join('')}
                <option value="new">New Size</option>
            </select>
        </td>
        <td>
            <select name="colourIds[]" class="form-control" required>
                <option value="" disabled selected>Select Colour</option>
                ${colours.map(colour => `<option value="${colour.colourId}">${colour.colourName}</option>`).join('')}
                <option value="new">New Colour</option>
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
            <button type="button" class="btn btn-danger remove-row-btn">-</button>
        </td>
    `;

        tbody.appendChild(newRow);
    });

    // Delegate event for removing row and adjusting quantity
    document.querySelector('tbody').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row-btn')) {
            if (document.querySelectorAll('.variant-row').length > 1) {
                e.target.closest('tr').remove();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Minimum Requirement',
                    text: 'You must have at least one variant.',
                    confirmButtonText: 'Okay'
                });
            }
        } else if (e.target.classList.contains('plus-quantity')) {
            let input = e.target.closest('.d-flex').querySelector('input');
            input.value = parseInt(input.value) + 1;
        } else if (e.target.classList.contains('minus-quantity')) {
            let input = e.target.closest('.d-flex').querySelector('input');
            if (input.value > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }
    });
});
