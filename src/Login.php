<?php
session_start();

if (isset($_SESSION["cust_login"]) || $_SESSION["cust_login"] !== "success") {
    header("Location: index.php");
    exit();
}
include 'includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>WhatTheDuck - Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
              integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
              crossorigin="anonymous">
        <link rel="stylesheet" href="css/main.css">
        <!-- Ensure jQuery is loaded first -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"
                integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
        <!-- Then load Bootstrap JS -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
                integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
        crossorigin="anonymous"></script>
        <!-- Load SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            .row {
                margin-top: 20px;
            }
            #box {
                border: 1px solid black;
                border-radius: 20px;
                box-shadow: 5px 10px #888888;
                box-sizing: border-box;
                padding: 40px;
            }
            body, .container, h1, p, a, .form-group, .btn {
                color: black;
            }
            .btn {
                background-color: #ffcc00;
                border: none;
                color: black;
            }
            .btn:hover {
                background-color: #ff6347;
                color: white;
            }
            a {
                color: blue;
                text-decoration: underline;
                font-weight: bold;
            }
            a:hover {
                color: #ff6347;
                text-decoration: underline;
            }
            .navbar a {
                color: black !important;
                text-decoration: none !important;
            }
            .navbar a:hover {
                text-decoration: none !important;
            }
        </style>
        <script>
            $(document).ready(function () {
                $("#cust-login").submit(function (event) {
                    event.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: "process_custlogin.php",
                        data: $(this).serialize(),
                        dataType: "json",
                        success: function (response) {
                            Swal.fire({
                                icon: response.icon,
                                title: response.title,
                                text: response.message,
                                showCloseButton: false,
                                showCancelButton: false,
                                confirmButtonText: response.icon === "success" ? "Return to Home" : "Return to Login",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = response.redirect || "Login.php";
                                }
                            });
                        }
                    });
                });
            });
        </script>
    </head>
    <body style="background-color:#fff5cc;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                    <h1>Login</h1>
                    <div class="login-form">
                        <form id="cust-login" method="post" accept-charset="UTF-8">
                            <div class="form-group" id="name-wrapper"> 
                                <input type="text" name="cust_username" id="cust_username" value="" class="form-control" placeholder="Username" required="">
                            </div>
                            <div class="form-group" id="password-wrapper"> 
                                <input type="password" name="cust_pass" id="cust_pass" value="" class="form-control" placeholder="Password" required="">
                            </div>
                            <input class="btn btn-primary" type="submit" name="op" id="submit" value="Sign In" class="notranslate form-submit">
                        </form>
                    </div>
                    <p>New to What The Duck? <a href="Signup.php">Sign up now!</a></p>
                    <p>Forgot your password? <a href="ForgetPassword.php">Click here</a></p>
                </div>        
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
    </body>
</html>
