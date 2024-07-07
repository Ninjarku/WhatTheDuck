<?php
session_start();
require_once 'jwt/jwt_cookie.php';
checkAuthentication('IT Admin');
include_once "includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>What The Duck - IT Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="js/jquery-3.5.1.js" type="text/javascript"></script>
    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
        integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm" crossorigin="anonymous">
        </script>
    <script defer src="js/datatables.min.js" type="text/javascript"></script>
    <link href="css/datatables.min.css" rel="stylesheet" type="text/css" />
    <script src="https://kit.fontawesome.com/70ab820747.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .btn-primary {
            background-color: #ffcc00;
            border: none;
            color: black;
        }

        .btn-primary:hover {
            background-color: #ff6347;
            color: white;
        }

        .icon-hover {
            color: black;
        }

        .btn-primary:hover .icon-hover {
            color: white;
        }
    </style>

    <script>
        $(document).ready(function () {
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
            loadTableData();

            $('#btnAddNew').click(function () {
                window.location.href = "user_form.php?action=addUser&Form_Type=0";
            });

            $("#user_table").on("click", ".btn-edit", function () {
                var User_ID = $(this).data("id");
                window.location.href = "user_form.php?action=editUser&Form_Type=1&User_ID=" + User_ID;
            });

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
                                        loadTableData();
                                    }
                                });
                            }
                        });
                    }
                });
            });
        });

        function loadTableData() {
            var table = $('#user_table').DataTable();
            table.clear().draw();

            $.ajax({
                type: "GET",
                url: "process_user.php?action=getAllUsers",
                cache: false,
                dataType: "JSON",
                success: function (response) {
                    if (response.icon === 'success') {
                        var users = response.data;
                        if (users.length === 0) {
                            table.row.add(['', '', '', '', '', '', '', '', '', '']).draw(false);
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
                                    user.Account_Active ? 'Active' : 'Inactive',
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
        <div class="table-content">
            <h1 class="text-center">User Management</h1>
            <button id="btnAddNew" class="btn btn-primary"><i class='fas fa-plus icon-hover'></i> Add New
                User</button>
            <br><br>
            <table id="user_table" class="display" style="width:100%">
            </table>
            <br><br>
        </div>
    </div>
    <?php include "includes/footer.php"; ?>
</body>

</html>