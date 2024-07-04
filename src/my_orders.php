<?php
session_start();

// Check if customer is logged in
if ($_SESSION["cust_rol"] !== "Customer") {
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
    <title>What The Duck</title>
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

    <!-- Custom JS for Order Management -->
    <script>
        $(document).ready(function () {
            // Initialize DataTables
            $('#pending_table').DataTable({
                "iDisplayLength": 5,
                "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
                columns: [
                    { title: "Order ID" },
                    { title: "Number of Items" },
                    { title: "Total Amount" },
                    { title: "Payment Type" },
                    { title: "Billing Adress" },
                    { title: "Order Status" },
                    { title: "Actions" }
                ],
                "deferRender": true
            });

            $('#history_table').DataTable({
                "iDisplayLength": 5,
                "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
                columns: [
                    { title: "Order ID" },
                    { title: "Number of Items" },
                    { title: "Total Amount" },
                    { title: "Payment Type" },
                    { title: "Billing Adress" },
                    { title: "Actions" }
                ],
            });

            // Load table data on page load
            loadTableData();
        });

        /// View order details
        $("#pending_table, #history_table").on("click", ".btn-view", function () {
            var order_id = $(this).data("id");
            window.location.href = "order_details.php?Order_ID=" + order_id;
        });

        // Set order status to received
        $("#pending_table").on("click", ".btn-received", function () {
            var Order_ID = $(this).data("id");
            Swal.fire({
                title: 'Are you sure you received this order?',
                text: "You won't be to do a refund!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, I received it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "process_orders.php",
                        data: {
                            action: "markAsReceived",
                            Order_ID: Order_ID
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
                                    loadTableData(); // Reload table data after update
                                }
                            });
                        }
                    });
                }
            });
        });

        // Function to load product data into the DataTable
        function loadTableData() {
            var pendingTable = $('#pending_table').DataTable();
            var historyTable = $('#history_table').DataTable();
            pendingTable.clear().draw();
            historyTable.clear().draw();

            $.ajax({
                type: "GET",
                url: "process_product.php?action=getAllProductsSales", // Fetch all products from process_product.php
                cache: false,
                dataType: "json",
                success: function (response) {
                    if (response.icon === 'success') {
                        var orders = response.data;
                        if (orders.length === 0) {
                            pendingTable.row.add(['', '', '', '', '', '', '']).draw(false); // If no data, add empty row
                            historyTable.row.add(['', '', '', '', '', '']).draw(false); // If no data, add empty row
                        } else {
                            orders.forEach(function (order) {
                                var action = `<button class='btn btn-view' data-id='${order.Order_ID}'><i class='fas fa-eye' style='padding-top: 0px;color:orange;'></i></button>`;
                                if (order.Order_Status === 'Pending') {
                                    action += `<button class='btn btn-received' data-id='${order.Order_ID}'><i class='fas fa-check' style='padding-top: 0px;color:green;'></i></button>`;
                                }
                                var row = [
                                    order.Order_ID,
                                    order.Total_Items,
                                    order.Total_Amount,
                                    order.Payment_Type,
                                    order.Billing_Address,
                                    order.Order_Status,
                                    action
                                ];
                                if (order.Order_Status === 'Pending') {
                                    pendingTable.row.add(row).draw(false);
                                } else if (order.Order_Status === 'Received') {
                                    historyTable.row.add(row).draw(false);
                                }
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
        <h1 class="text-center">Purchase Order List</h1>
        <br><br>
        <h2>Pending Orders</h2>
        <table id="pending_table" class="display" style="width:100%"></table>
        <br><br>
        <h2>Order History</h2>
        <table id="history_table" class="display" style="width:100%"></table>
        <br><br>
    </div>
    <?php include "includes/footer.php"; ?>
</body>

</html>