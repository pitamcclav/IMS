document.addEventListener('DOMContentLoaded', function () {
    // Open modal when 'New' option is selected
    document.querySelector('select[name="supplierid"]').addEventListener('change', function (e) {
        if (e.target.value === 'new') {
            new bootstrap.Modal(document.getElementById('newSupplierModal')).show();
        }
    });

    document.querySelector('select[name="itemid"]').addEventListener('change', function (e) {
        if (e.target.value === 'new') {
            new bootstrap.Modal(document.getElementById('newItemModal')).show();
        }
    });

    document.querySelector('select[name="colourid"]').addEventListener('change', function (e) {
        if (e.target.value === 'new') {
            new bootstrap.Modal(document.getElementById('newColorModal')).show();
        }
    });

    document.querySelector('select[name="sizeid"]').addEventListener('change', function (e) {
        if (e.target.value === 'new') {
            new bootstrap.Modal(document.getElementById('newSizeModal')).show();
        }
    });
    // Add client-side validation for quantity
    document.querySelector('input[name="quantity"]').addEventListener('input', function () {
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
        console.log(colorName)

        $.ajax({
            url: '/api/colour',
            type: 'POST',
            data: {
                colorName: colorName
            },
            success: function(response) {
                if (response.success) {
                    $('#colourid').append(new Option(response.color.colourName, response.color.colourId));
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
});
