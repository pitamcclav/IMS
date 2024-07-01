document.addEventListener('DOMContentLoaded', () => {
    "use strict";

    // Add request detail
    function addRequestDetail() {
        const detailDiv = document.createElement('div');
        detailDiv.classList.add('requestDetail', 'mb-3');

        const itemOptions = document.getElementById('itemOptions').innerHTML;
        const colourOptions = document.getElementById('colourOptions').innerHTML;
        const sizeOptions = document.getElementById('sizeOptions').innerHTML;

        detailDiv.innerHTML = `
            <label for="item">Item</label>
            <select name="itemIds[]" class="form-control mb-2">
                ${itemOptions}
            </select>
            <label for="quantity">Quantity</label>
            <input type="number" name="quantities[]" class="form-control mb-2" placeholder="Quantity">
            <label for="colour">Colour</label>
            <select name="colourIds[]" class="form-control mb-2">
                ${colourOptions}
            </select>
            <label for="size">Size</label>
            <select name="sizeIds[]" class="form-control mb-2">
                ${sizeOptions}
            </select>
            <button type="button" class="btn btn-danger btn-sm removeDetail">Remove</button>
            <hr>
        `;

        document.getElementById('requestDetails').appendChild(detailDiv);

        // Add event listener for remove button
        detailDiv.querySelector('.removeDetail').addEventListener('click', function () {
            detailDiv.remove();
        });
    }

    document.getElementById('addRequestDetailBtn').addEventListener('click', addRequestDetail);

    // Add event listener for remove buttons on existing details
    document.querySelectorAll('.removeDetail').forEach(button => {
        button.addEventListener('click', function () {
            button.parentElement.remove();
        });
    });
});
