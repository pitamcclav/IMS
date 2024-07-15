<div class="modal fade" id="assignRoleModal" tabindex="-1" role="dialog" aria-labelledby="assignRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignRoleModalLabel">Assign Roles to <span id="assignRoleUserName"></span></h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="rolesForm" method="POST" action="{{ route('roles.assign') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="staff_id" id="assignRoleUserId" value="">
                    <div class="form-group">
                        <label for="roles">Assign Roles</label>
                        <select name="roles" id="roles" class="form-control" >
                            <option value="" disabled>Select Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Assign Roles</button>
                </div>
            </form>
        </div>
    </div>
</div>
