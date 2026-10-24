

document.addEventListener("DOMContentLoaded", () => {

    const modal =
        document.getElementById("addOrderRestockModal");

    const openBtn =
        document.getElementById("order-restock-add-btn");

    const ordcancelbtn =
        document.getElementById("ord-cancel-btn");


    // Open modal when ADD / CREATE ORDER button is clicked
    if (openBtn && modal) {

        openBtn.addEventListener("click", () => {

            modal.style.display = "block";

        });

    }


    // Close modal when cancel button is clicked
    if (ordcancelbtn && modal) {

        ordcancelbtn.addEventListener("click", () => {

            modal.style.display = "none";

        });

    }


    // Close modal when clicking outside modal content
    if (modal) {

        window.addEventListener("click", (event) => {

            if (event.target === modal) {

                modal.style.display = "none";

            }

        });

    }

});