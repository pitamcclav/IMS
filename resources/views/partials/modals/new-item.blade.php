<div class="modal fade" id="newItemModal" tabindex="-1" role="dialog" aria-labelledby="newItemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newItemModalLabel">Add New Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form id="newItemForm">

                    <div class="form-group">
                        <label for="newItemName">Item Name</label>
                        <input type="text" class="form-control" id="newItemName" required>
                    </div>
                    <div class="form-group">
                        <label for="newItemDescription">Description</label>
                        <input type="text" class="form-control" id="newItemDescription" required>
                    </div>
                    <div class="form-group">
                        <label for="newItemCategory">Category</label>
                        <select class="form-control" id="newItemCategory" required>
                            <option value="" disabled selected>Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->categoryId }}">{{ $category->categoryName }}</option>
                            @endforeach
                        </select>
                    </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
