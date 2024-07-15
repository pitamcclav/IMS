<div class="modal fade" id="newSupplierModal" tabindex="-1" role="dialog" aria-labelledby="newSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newSupplierModalLabel">Add New Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newSupplierForm">
                    <div class="form-group">
                        <label for="supplierName">Supplier Name</label>
                        <input type="text" id="newSupplierName" name="supplierName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="supplierContact">Supplier Contact</label>
                        <input type="email" id="newSupplierContact" name="supplierContact" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Supplier</button>
                </form>
            </div>
        </div>
    </div>
</div>
