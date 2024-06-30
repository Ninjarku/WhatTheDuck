<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // Start the session at the beginning of the file
// Check if the admin is logged in
if ($_SESSION["admin_login"] !== "success") {
    header("Location: error_page.php?error_id=0&error=" . urlencode("Please login!!")); // Redirect to login page
    exit();
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
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
              integrity=
              "sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
              crossorigin="anonymous"> 
        <!-- jQuery -->
        <script src="js/jquery-3.5.1.js" type="text/javascript"></script>
        <!--Bootstrap JS-->
        <script defer
                src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
                integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
                crossorigin="anonymous">
        </script> 
        <!-- DataTables JS -->
        <script defer src="js/datatables.min.js" type="text/javascript"></script>  
        <!-- DataTables CSS -->
        <link href="css/datatables.min.css" rel="stylesheet" type="text/css"/>
        <!-- FontAwesome for Icons -->
        <script src="https://kit.fontawesome.com/70ab820747.js" crossorigin="anonymous"></script>
        <!-- SweetAlert2 for Popups -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- END OF THE LINK -->

        <!-- Custom JS for pro_tableduct Management -->
        <script>
                    $(document).ready(function () {
                        // Initialize DataTable
                        $('#product_table').DataTable({
                            "iDisplayLength": 5,
                            "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
                            data: $("#product_table tbody").html(),
                            columns: [
                                {title: "Product ID"},
                                {title: "Product Name"},
                                {title: "Product Description"},
                                {title: "Product Image"},
                                {title: "Price"},
                                {title: "Quantity"},
                                {title: "Product of Category"},
                                {title: "Product Available"},
                                {title: "Actions"}
                            ],
                            "deferRender": true
                        });

                        // Load table data on page load
                        loadTableData();

                        // Add new pro_tableduct button click event
                        $('#btnAddNew').click(function () {
                            window.location.href = "product_form.php?action=addProduct&Form_Type=0";
                        });

                        // Edit pro_tableduct button click event
                        $("#product_table").on("click", "#btnEdit", function () {
                            var Product_ID = $(this).val();
                            window.location.href = "product_form.php?action=editProduct&Form_Type=1&Product_ID=" + Product_ID; // Redirect to product_table.php for editing the pro_tableduct
                        });

                        // Delete pro_tableduct button click event
                        $("#product_table").on("click", "#btnDelete", function () {
                            var Product_ID = $(this).val();
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
                                        type: "GET",
                                        url: "process_product.php?action=deleteProduct&Product_ID=" + Product_ID,
                                        cache: false,
                                        success: function (result) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Deleted successfully!',
                                                showCloseButton: false,
                                                showCancelButton: false,
                                                confirmButtonText: 'Ok'
                                            }).then(() => {
                                                loadTableData(); // Reload table data after deletion
                                            });
                                        }
                                    });
                                }
                            });
                        });
                    });

                    // Function to load pro_tableduct data into the DataTable
                    function loadTableData() {
                        var table = $('#product_table').DataTable();
                        table.clear().draw();

                        $.ajax({
                            type: "GET",
                            url: "process_product.php?action=getAllProducts", // Fetch all pro_tableduct from pro_tableduct.php
                            cache: false,
                            dataType: "JSON",
                            success: function (result) {
                                console.log(result);
                                if (result.length === 0) {
                                    table.row.add(['', '', '', '', '', '', '', '', '', '']).draw(false); // If no data, add empty row
                                } else {
                                    result.forEach(function (product) {
                                        var action = "<button id='btnEdit' value='" + product.Product_ID + "' class='btn btn' aria-label='edit-staff' style='border: 1px solid #A9A9A9;'><i class='fas fa-edit' style='padding-top: 0px;color:orange;'></i></button><button id='btnDelete' value='" + product.Product_ID + "' class='btn btn' style='border: 1px solid #A9A9A9;'><i class='fas fa-trash' style='padding-top: 0px;color:red;'></i></button>";
                                        table.row.add([
                                            product.Product_ID,
                                            product.Product_Name,
                                            product.Product_Description,
                                            product.Product_Image,
                                            product.Price,
                                            product.Quantity,
                                            product.Product_Category,
                                            product.Product_Available,
                                            action
                                        ]).draw(false);
                                    });
                                }
                            }
                        });
                    }
                    ;
        </script>
    </head>

    <body>
        <?php include "includes/navbar.php"; ?>
        <div class="container">
            <h1 class="text-center">Product Management</h1>
            <button id="btnAddNew" class="btn btn-primary"><i class='fas fa-plus' style="color:white;"></i> Add New Product</button>
            <br><br>
            <table id="product_table" class="display" style="width:100%">
            </table>
            <br><br>
        </div>
        <?php include "includes/footer.php"; ?>
    </body>
</html>