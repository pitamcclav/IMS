<div class="modal fade" id="newStoreModal" tabindex="-1" role="dialog" aria-labelledby="newStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newStoreModalLabel">Add New Store</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form id="newStoreForm">
                    <div class="form-group">
                        <label for="storeName">Store Name</label>
                        <input type="text" class="form-control" id="storeName" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" id="location" required>
                    </div>
                    <div class="form-group">
                        <label for="staff">Staff In-charge</label>
                        <select class="form-control" id="staff" >
                            <option value="" disabled selected>Select staff</option>
                            @foreach ($staff as $user)
                                <option value="{{ $user->staffId }}">{{ $user->staffName }}</option>
                            @endforeach
                        </select>
                    </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Store</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
