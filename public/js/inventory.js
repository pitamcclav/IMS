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
    document.querySelector('input[name="quantities[]"]').addEventListener('input', function () {
        let quantity = this.value.trim();
        if (quantity !== '' && isNaN(quantity)) {
            this.setCustomValidity('Quantity must be a number');
        } else {
            this.setCustomValidity('');
        }
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
                    alert('Supplier added successfully');
                } else {
                    alert('Failed to add supplier');
                }
            },
            error: function(response) {
                alert('Error: ' + response.responseText);
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
                    alert('Item added successfully');
                } else {
                    alert('Failed to add item');
                }
            },
            error: function(response) {
                alert('Error: ' + response.responseText);
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
                    $('#colourid').append(new Option(response.colour.colourName, response.colour.colourId));
                    $('#newColorModal').modal('hide');
                    document.getElementById('newColorName').value = '';
                    alert('Color added successfully');
                } else {
                    alert('Failed to add color');
                }
            },
            error: function(response) {
                alert('Error: ' + response.responseText);
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
                    $('#sizeid').append(new Option(response.size.sizeValue, response.size.sizeId));
                    $('#newSizeModal').modal('hide');
                    document.getElementById('newSizeValue').value = '';
                    alert('Size added successfully');
                } else {
                    alert('Failed to add size');
                }
            },
            error: function(response) {
                alert('Error: ' + response.responseText);
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
                <div class="d-flex align-items-center" >
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
                alert('You must have at least one variant.');
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
