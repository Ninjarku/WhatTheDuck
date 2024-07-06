<?php
session_start();

// Check if customer is logged in
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
    <!-- Styling for Table -->
    <style>
        body,
        html {
            font-family: 'Comic Neue', cursive;
            background-color: #fff5cc;
            color: black;
        }

        .table-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container {
            margin-top: 50px;
            margin-bottom: 50px;
        }
    </style>
    <!-- END OF THE LINK -->

    <!-- Custom JS for Order Management -->
    <script>
        $(document).ready(function () {
            // Initialize DataTables
            $('#pending_table').DataTable({
                "iDisplayLength": 5,
                "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
                columns: [
                    { title: "Order No." },
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
                    { title: "Order No." },
                    { title: "Number of Items" },
                    { title: "Total Amount" },
                    { title: "Payment Type" },
                    { title: "Billing Adress" },
                    { title: "Actions" }
                ],
            });

            // Load table data on page load
            loadTableData();

            /// View order details
            $("#pending_table, #history_table").on("click", ".btn-view", function () {
                var Order_Num = $(this).data("id");
                window.location.href = "order_details.php?Order_Num=" + Order_Num;
            });

            // Set order status to received
            $("#pending_table").on("click", ".btn-shipped", function () {
                var Order_Num = $(this).data("id");
                Swal.fire({
                    title: 'Are you sure you approve this order?',
                    text: "You won't be to revert this action!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, I shipped it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "process_order.php",
                            data: {
                                action: "markAsShipped",
                                Order_Num: Order_Num
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
        });

        // Function to load product data into the DataTable
        function loadTableData() {
            var pendingTable = $('#pending_table').DataTable();
            var historyTable = $('#history_table').DataTable();
            pendingTable.clear().draw();
            historyTable.clear().draw();

            $.ajax({
                type: "GET",
                url: "process_order.php?action=getAllOrders", // Fetch all products from process_product.php
                cache: false,
                dataType: "json",
                success: function (response) {
                    if (response.icon === 'success') {
                        var pendingOrders = response.pendingOrders;
                        var historyOrders = response.historyOrders;

                        if (pendingOrders.length === 0) {
                            pendingTable.row.add(['', '', '', '', '', '', '']).draw(false); // If no data, add empty row
                        } else {
                            pendingOrders.forEach(function (order) {
                                var action = `<button class='btn btn-view' data-id='${order.Order_Num}'><i class='fas fa-eye' style='padding-top: 0px;color:orange;'></i></button>
                                              <button class='btn btn-shipped' data-id='${order.Order_Num}'><i class='fas fa-check' style='padding-top: 0px;color:green;'></i></button>`;
                                var row = [
                                    order.Order_Num,
                                    order.Number_of_Items,
                                    "$" + order.Total_Amount,
                                    order.Payment_Type,
                                    order.Billing_Address,
                                    order.Order_Status,
                                    action
                                ];
                                pendingTable.row.add(row).draw(false);
                            });
                        }

                        if (historyOrders.length === 0) {
                            historyTable.row.add(['', '', '', '', '', '']).draw(false); // If no data, add empty row
                        } else {
                            historyOrders.forEach(function (order) {
                                var action = `<button class='btn btn-view' data-id='${order.Order_Num}'><i class='fas fa-eye' style='padding-top: 0px;color:orange;'></i></button>`;
                                var row = [
                                    order.Order_Num,
                                    order.Number_of_Items,
                                    "$" + order.Total_Amount,
                                    order.Payment_Type,
                                    order.Billing_Address,
                                    action
                                ];
                                historyTable.row.add(row).draw(false);
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
        <div class="table-content">
            <h1 class="text-center">Order List</h1>
            <br><br>
            <h2>Pending Orders</h2>
            <table id="pending_table" class="display" style="width:100%"></table>
            <br><br>
            <h2>Order History</h2>
            <table id="history_table" class="display" style="width:100%"></table>
            <br><br>
        </div>
    </div>
    <?php include "includes/footer.php"; ?>
</body>

</html>