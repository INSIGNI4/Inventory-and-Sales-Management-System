




document.addEventListener('DOMContentLoaded', function () {

    console.log("EDIT_PRODUCT_NEW_VERSION_LOADED");

    const editButtons =
        document.querySelectorAll('.select-product-btn');

    const modal =
        document.getElementById('editProductModal');


    editButtons.forEach(button => {

        button.addEventListener('click', function () {

            // Show modal
            if (modal) {
                modal.style.display = 'block';
            }

            // Fill form fields with data attributes
            const editId =
                document.getElementById('edit-id');

            const editName =
                document.getElementById('edit-name');

            const editType =
                document.getElementById('edit-type');

            const editStorePrice =
                document.getElementById('edit-storeprice');

            // const editSupplierPrice =
            //     document.getElementById('edit-supplierprice');

            // const editSupplierId =
            //     document.getElementById('edit-supplierid');

            const editPackSize =
                document.getElementById('edit-packsize');

            const editBarcode =
                document.getElementById('edit-barcode');


            if (editId) {
                editId.value = this.dataset.id;
            }

            if (editName) {
                editName.value = this.dataset.name;
            }

            if (editType) {
                editType.value = this.dataset.type;
            }

            if (editStorePrice) {
                editStorePrice.value = this.dataset.storeprice;
            }

            // if (editSupplierPrice) {
            //     editSupplierPrice.value =
            //         this.dataset.supplierprice;
            // }

            // if (editSupplierId) {
            //     editSupplierId.value =
            //         this.dataset.supplierid;
            // }

            if (editPackSize) {
                editPackSize.value =
                    this.dataset.packsize;
            }

            if (editBarcode) {
                editBarcode.value =
                    this.dataset.barcode;
            }

        });

    });


    // Cancel button
    const cancelButton =
        document.getElementById('editprod-cancel-btn');

    if (cancelButton && modal) {

        cancelButton.addEventListener('click', function () {

            modal.style.display = 'none';

        });

    }


    // Optional: Close modal when clicking outside of it
    if (modal) {

        window.addEventListener('click', function (event) {

            if (event.target === modal) {

                modal.style.display = 'none';

            }

        });

    }


    // Expiration toggle
    const nulltoggleBtn =
        document.getElementById('toggleNullBtn1');

    const expInput =
        document.getElementById('edit-expirationdate');

    const notnulltoggleBtn =
        document.getElementById('toggleNotNullBtn1');


    if (
        nulltoggleBtn &&
        notnulltoggleBtn &&
        expInput
    ) {

        nulltoggleBtn.addEventListener(
            'click',
            function () {

                nulltoggleBtn.style.display = 'none';

                notnulltoggleBtn.style.display =
                    'inline-block';

                notnulltoggleBtn.style.color =
                    'blue';

                expInput.disabled = true;

                expInput.value = "";

            }
        );


        notnulltoggleBtn.addEventListener(
            'click',
            function () {

                notnulltoggleBtn.style.display =
                    'none';

                nulltoggleBtn.style.display =
                    'inline-block';

                nulltoggleBtn.style.color =
                    'red';

                expInput.disabled = false;

            }
        );

    }

});


const editproductbtn =
    document.getElementById("edit-product-btn");

const deleteproductbtn =
    document.getElementById("delete-product-btn");

const displaydeletebtn =
    document.getElementById("display-delete");


function resetActions() {

    document
        .querySelectorAll(".select-product-btn")
        .forEach(btn => {
            btn.style.display = "none";
        });

    document
        .querySelectorAll(".delete-product-form")
        .forEach(form => {
            form.style.display = "none";
        });

}


if (editproductbtn) {

    editproductbtn.addEventListener("click", () => {

        resetActions();

        document
            .querySelectorAll(".select-product-btn")
            .forEach(btn => {
                btn.style.display = "inline-block";
            });

    });

}


if (deleteproductbtn) {

    deleteproductbtn.addEventListener("click", () => {

        resetActions();

        document
            .querySelectorAll(".delete-product-form")
            .forEach(form => {
                form.style.display = "inline-block";
            });

    });

}



