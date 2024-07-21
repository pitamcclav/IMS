document.addEventListener('DOMContentLoaded', () => {
    "use strict";

    // Sidebar toggle behavior
    function toggleSidebar() {
        const hamBurger = document.querySelector(".toggle-btn");

        hamBurger.addEventListener("click", function () {
            document.querySelector("#sidebar").classList.toggle("expand");
        });
    }
    toggleSidebar();

    let deleteUrl = ''; // This will store the delete URL

    // Function to open the delete modal
    function openDeleteModal(url) {
        deleteUrl = url; // Set the delete URL
        const deleteModalElement = document.getElementById('deleteModal');
        if (deleteModalElement) {
            const deleteModal = new bootstrap.Modal(deleteModalElement);
            deleteModal.show();
        } else {
            console.error('Delete modal element not found!');
        }
    }

    // Delegate event listener for delete buttons
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            openDeleteModal(url);
        });
    });

    // Handle the confirm delete button click
    const confirmDeleteButton = document.getElementById('confirmDelete');
    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', function (e) {
            e.preventDefault();

            // Send DELETE request to the server
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'The item has been deleted successfully.',
                        confirmButtonText: 'Okay'
                    }).then(() => {
                        // Reload the page or update the UI as needed
                        window.location.reload();
                    });
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });
    }

    // CSRF token setup for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

});
