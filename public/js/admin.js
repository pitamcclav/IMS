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
                    window.location.reload();
                } else {
                    alert('An error occurred. Please try again.');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
});
