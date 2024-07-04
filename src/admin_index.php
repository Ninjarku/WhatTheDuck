<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if it admin is logged in
if ($_SESSION["cust_rol"] !== "IT Admin") {
    ?>
    <script>
        window.location.href = 'error_page.php?error_id=0&error=' + encodeURIComponent('Please login!!');
    </script>
    <?php
    exit(); // Make sure to stop further execution after redirection
} else {
    include "includes/navbar.php";
    // Your other code for IT Admin
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>What The Duck - IT Admin</title>
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

    <!-- Custom JS for User Management -->
    <script>
        $(document).ready(function () {
            // Initialize DataTable
            $('#user_table').DataTable({
                "iDisplayLength": 5,
                "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
                columns: [
                    { title: "User ID" },
                    { title: "Username" },
                    { title: "Email" },
                    { title: "Mobile Number" },
                    { title: "Billing Address" },
                    { title: "Gender" },
                    { title: "Date of Birth" },
                    { title: "User Type" },
                    { title: "Account Active" },
                    { title: "Actions" }
                ],
                "deferRender": true
            });

            // Load table data on page load
            loadTableData();

            // Add new user button click event
            $('#btnAddNew').click(function () {
                window.location.href = "user_form.php?action=addUser&Form_Type=0";
            });

            // Edit user button click event
            $("#user_table").on("click", "#btnEdit", function () {
                var User_ID = $(this).val();
                window.location.href = "user_form.php?action=editUser&Form_Type=1&User_ID=" + User_ID; // Redirect to user_form.php for editing the user
            });

            // Delete user button click event
            $("#user_table").on("click", ".btn-delete", function () {
                var User_ID = $(this).data("id");
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
                            url: "process_user.php",
                            data: {
                                action: "deleteUser",
                                User_ID: User_ID
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

        // Function to load user data into the DataTable
        function loadTableData() {
            var table = $('#user_table').DataTable();
            table.clear().draw();

            $.ajax({
                type: "GET",
                url: "process_user.php?action=getAllUsers", // Fetch all users from process_user.php
                cache: false,
                dataType: "JSON",
                success: function (response) {
                    if (response.icon === 'success') {
                        var users = response.data;
                        if (users.length === 0) {
                            table.row.add(['', '', '', '', '', '', '', '', '', '']).draw(false); // If no data, add empty row
                        } else {
                            users.forEach(function (user) {
                                var action = `<button class='btn btn-edit' data-id='${user.User_ID}'><i class='fas fa-edit' style='padding-top: 0px;color:orange;'></i></button>
                                          <button class='btn btn-delete' data-id='${user.User_ID}'><i class='fas fa-trash' style='padding-top: 0px;color:red;'></i></button>`;
                                table.row.add([
                                    user.User_ID,
                                    user.Username,
                                    user.Email,
                                    user.Mobile_Number,
                                    user.Billing_Address,
                                    user.Gender,
                                    user.DOB,
                                    user.User_Type,
                                    user.Account_Active,
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
        <h1 class="text-center">User Management</h1>
        <button id="btnAddNew" class="btn btn-primary"><i class='fas fa-plus' style="color:white;"></i> Add New
            User</button>
        <br><br>
        <table id="user_table" class="display" style="width:100%">
        </table>
        <br><br>
    </div>
    <?php include "includes/footer.php"; ?>
</body>

</html>