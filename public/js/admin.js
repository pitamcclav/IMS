document.addEventListener('DOMContentLoaded', () => {
    "use strict";

    function openModal(modalId) {
        new bootstrap.Modal(document.getElementById(modalId)).show();
    }

    document.querySelectorAll('.assign-role-btn').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-userid');
            const userName = this.getAttribute('data-username');
            document.getElementById('assignRoleUserId').value = userId;
            document.getElementById('assignRoleUserName').textContent = userName;
            openModal('assignRoleModal');
        });
    });

    $('#rolesForm').on('submit', function (event) {
        event.preventDefault();
        let form = $(this);
        let formData = form.serialize();

        console.log('formData:', formData);

        $.ajax({
            url: '/assign-roles',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Role Assigned!',
                        text: 'The role has been successfully assigned.',
                        confirmButtonText: 'Okay'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while assigning the role. Please try again.',
                        confirmButtonText: 'Okay'
                    });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An unexpected error occurred. Please try again later.',
                    confirmButtonText: 'Okay'
                });
                console.error('Error:', error);
            }
        });
    });
});
