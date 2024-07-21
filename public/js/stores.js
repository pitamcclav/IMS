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
                Swal.fire({
                    icon: 'success',
                    title: 'Store Added!',
                    text: 'The new store has been added successfully.',
                    confirmButtonText: 'Okay'
                }).then(() => {
                    // Reset the form
                    newStoreForm.reset();

                    // Refresh the page
                    window.location.reload();

                    // Alternatively, you can append the new store row to the table if the page does not need a full reload
                    let storeTableBody = document.getElementById('storeTable').getElementsByTagName('tbody')[0];
                    storeTableBody.insertAdjacentHTML('beforeend', response.storeRow);
                });
            },
            error: function (response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'There was an issue adding the store. Please try again.',
                    confirmButtonText: 'Okay'
                });
            }
        });
    });

});
