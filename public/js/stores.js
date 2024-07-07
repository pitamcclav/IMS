document.addEventListener('DOMContentLoaded', function () {
    "use strict";

    // Function to open the respective modal
    function openModal(modalId) {
        new bootstrap.Modal(document.getElementById(modalId)).show();
    }

    // Delegate event listener for 'New' option in select elements
    document.getElementById('newStore').addEventListener('click', function (e) {
        openModal('newStoreModal');
    });


    // CSRF token setup for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle form submission
    const newStoreForm = document.getElementById('newStoreForm');

    newStoreForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        // Fetch form data
        let storeName = document.getElementById('storeName').value;
        let location = document.getElementById('location').value;
        let staffId = document.getElementById('staff').value;

        console.log(storeName);
        console.log(location);
        console.log(staffId);

        // Send POST request to the server
        $.ajax({
            url: '/stores/add',
            type: 'POST',
            data: {
                storeName: storeName,
                location: location,
                staffId: staffId
            },
            success: function (response) {
                // Close the modal
                console.log(response);
                $('#newStoreModal').modal('hide');
                // Reset the form
                newStoreForm.reset();

                // Refresh the page
                window.location.reload();

                // Reset the form
                newStoreForm.reset();

                // Append the new store row to the table body
                let storeTableBody = document.getElementById('storeTable').getElementsByTagName('tbody')[0];
                storeTableBody.insertAdjacentHTML('beforeend', response.storeRow);
            },
            error: function (response) {
                console.log(response);
            }
        });
    });

});
