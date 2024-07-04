<?php
session_start();

// Check if sales admin is logged in
if ($_SESSION["cust_rol"] !== "Sales Admin") {
    ?>
    <script>
        window.location.href = 'error_page.php?error_id=0&error=' + encodeURIComponent('Please login!!');
    </script>
    <?php
    exit();
} else {
    include "includes/navbar.php";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>What The Duck - Sales Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- START OF THE LINK -->
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <!-- jQuery -->
    <script src="js/jquery-3.5.1.js" type="text/javascript"></script>
    <!--Bootstrap JS-->
    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
        integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm" crossorigin="anonymous">
        </script>
    <!-- DataTables JS -->
    <script defer src="js/datatables.min.js" type="text/javascript"></script>
    <!-- DataTables CSS -->
    <link href="css/datatables.min.css" rel="stylesheet" type="text/css" />
    <!-- FontAwesome for Icons -->
    <script src="https://kit.fontawesome.com/70ab820747.js" crossorigin="anonymous"></script>
    <!-- SweetAlert2 for Popups -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- END OF THE LINK -->

    <!-- Custom JS for Product Management -->
    <script>
        $(document).ready(function () {
            // Initialize DataTable
            $('#product_table').DataTable({
                "iDisplayLength": 5,
                "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
                columns: [
                    { title: "Product ID" },
                    { title: "Product Name" },
                    { title: "Product Image" },
                    { title: "Product Description" },
                    { title: "Price" },
                    { title: "Quantity" },
                    { title: "Product Category" },
                    { title: "Product Available" },
                    { title: "Actions" }
                ],
                "deferRender": true
            });

            // Load table data on page load
            loadTableData();

            // Add new product button click event
            $('#btnAddNew').click(function () {
                window.location.href = "product_form.php?action=addProduct&Form_Type=0";
            });

            // Edit product button click event
            $("#product_table").on("click", ".btn-edit", function () {
                var Product_ID = $(this).data("id");
                window.location.href = "product_form.php?action=editProduct&Form_Type=1&Product_ID=" + Product_ID; // Redirect to product_form.php for editing the product
            });

            // Delete product button click event
            $("#product_table").on("click", ".btn-delete", function () {
                var Product_ID = $(this).data("id");
                Swal.fire({
                    title: 'Are you sure you would like to delete?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "process_product.php",
                            data: {
                                action: "deleteProduct",
                                Product_ID: Product_ID
                            },
                            dataType: "json",
                            success: function (response) {
                                Swal.fire({
                                    icon: response.icon,
                                    title: response.title,
                                    text: response.message,
                                    showCloseButton: false,
                                    showCancelButton: false,
                                    confirmButtonText: 'Ok'
                                }).then(() => {
                                    if (response.icon === 'success') {
                                        loadTableData(); // Reload table data after deletion
                                    }
                                });
                            }
                        });
                    }
                });
            });
        });

        // Function to load product data into the DataTable
        function loadTableData() {
            var table = $('#product_table').DataTable();
            table.clear().draw();

            $.ajax({
                type: "GET",
                url: "process_product.php?action=getAllProductsSales", // Fetch all products from process_product.php
                cache: false,
                dataType: "json",
                success: function (response) {
                    if (response.icon === 'success') {
                        var products = response.data;
                        if (products.length === 0) {
                            table.row.add(['', '', '', '', '', '', '', '', '']).draw(false); // If no data, add empty row
                        } else {
                            products.forEach(function (product) {
                                var action = `<button class='btn btn-edit' data-id='${product.Product_ID}'><i class='fas fa-edit' style='padding-top: 0px;color:orange;'></i></button>
                                              <button class='btn btn-delete' data-id='${product.Product_ID}'><i class='fas fa-trash' style='padding-top: 0px;color:red;'></i></button>`;
                                var image = product.Product_Image ? "<img src='data:image/jpeg;base64," + product.Product_Image + "' alt='Product Image' class='img-thumbnail' style='max-height: 100px;'>" : "No image";
                                table.row.add([
                                    product.Product_ID,
                                    product.Product_Name,
                                    image,
                                    product.Product_Description,
                                    product.Price,
                                    product.Quantity,
                                    product.Product_Category,
                                    product.Product_Available ? 'Yes' : 'No',
                                    action
                                ]).draw(false);
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: response.icon,
                            title: response.title,
                            text: response.message,
                            showCloseButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'Ok'
                        });
                    }
                }
            });
        }
    </script>
</head>

<body>
    <div class="container">
        <br><br>
        <h1 class="text-center">Product Management</h1>
        <button id="btnAddNew" class="btn btn-primary"><i class='fas fa-plus' style="color:white;"></i> Add New
            Product</button>
        <br><br>
        <table id="product_table" class="display" style="width:100%"></table>
        <br><br>
    </div>
    <?php include "includes/footer.php"; ?>
</body>

</html>