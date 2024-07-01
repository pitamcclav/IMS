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

});
