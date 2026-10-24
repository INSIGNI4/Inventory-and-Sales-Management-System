document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("addProductModal");
    const openBtn = document.getElementById("products-add-btn");
    const cancelBtn = document.getElementById("cancel-btn");



    // Open modal when ADD button is clicked
    openBtn.addEventListener("click", () => {
        modal.style.display = "block";
    });

    // Close modal when cancel button is clicked
    cancelBtn.addEventListener("click", () => {
        modal.style.display = "none";
    }); 

    // Close modal when clicking outside modal content
    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });




});






    