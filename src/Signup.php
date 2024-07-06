<?php
session_start();
include 'includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>WhatTheDuck - Sign up</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="css/main.css">
    <!-- jQuery -->
    <script defer src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
        integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .form-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .form-control {
            width: 233px;
            box-sizing: border-box;
        }

        label {
            flex: 1;
            margin-right: 10px;
            color: black;
        }

        body,
        .container,
        h1,
        p,
        a,
        .form-group,
        .btn {
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

        .container {
            margin-top: 50px;
            margin-bottom: 50px;
        }
    </style>
</head>

<body style="background-color:#fff5cc;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5 col-xl-5">
                <h1>Sign Up</h1>
                <?php
                session_start();
                if (isset($_SESSION['signup_error'])) {
                    echo '<p>' . $_SESSION['signup_error'] . '</p>';
                    unset($_SESSION['signup_error']);
                }
                ?>
                <div class="signup-form">
                    <form id="cust-signup" method="post">
                        <div class="form-group">
                            <label for="signup_username">Username:</label>
                            <input class="form-control" type="text" id="signup_username" maxlength="45" required
                                name="signup_username" placeholder="Enter username">
                        </div>
                        <div class="form-group">
                            <label for="signup_email">Email:</label>
                            <input class="form-control" type="email" id="signup_email" required name="signup_email"
                                placeholder="Enter email">
                        </div>
                        <div class="form-group">
                            <label for="signup_mobile_number">Mobile Number:</label>
                            <input class="form-control" type="text" id="signup_mobile_number" maxlength="8" required
                                name="signup_mobile_number" placeholder="Enter mobile number">
                        </div>
                        <div class="form-group">
                            <label for="signup_birthday">Date of Birth:</label>
                            <input class="form-control" type="date" required name="signup_birthday"
                                id="signup_birthday">
                        </div>
                        <div class="form-group">
                            <label for="signup_pwd">Password:</label>
                            <input class="form-control" type="password" id="signup_pwd" required name="signup_pwd"
                                placeholder="Enter password">
                            <button type="button" id="show-password" class="btn btn-secondary">Show Password</button>
                            <div id="password-strength"></div>
                        </div>
                        <div class="form-group">
                            <label for="signup_pwdconfirm">Confirm Password:</label>
                            <input class="form-control" type="password" id="signup_pwdconfirm" required
                                name="signup_pwdconfirm" placeholder="Confirm password">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" style="width: 100%;" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
                <p>Have an account? <a href="Login.php">Login Now!</a></p>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script src="js/zxcvbn.js"></script>
    <script>
        $(document).ready(function () {
            $('#signup_pwd').on('input', function () {
                var password = $(this).val();
                var result = zxcvbn(password);
                var score = result.score;
                var feedback = result.feedback.suggestions.join(' ');

                var strengthText;
                switch (score) {
                    case 0:
                    case 1:
                        strengthText = "Weak";
                        break;
                    case 2:
                        strengthText = "Fair";
                        break;
                    case 3:
                        strengthText = "Good";
                        break;
                    case 4:
                        strengthText = "Strong";
                        break;
                }

                $('#password-strength').text('Strength: ' + strengthText + '. ' + feedback);
            });

            $('#show-password').on('click', function () {
                var passwordField = $('#signup_pwd');
                var passwordFieldType = passwordField.attr('type');
                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).text('Hide Password');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).text('Show Password');
                }
            });

            $("#cust-signup").on("submit", function (event) {
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "process_custsignup.php",
                    data: $(this).serialize(),
                    success: function (response) {
                        Swal.fire({
                            icon: response.icon,
                            title: response.title,
                            text: response.message,
                            showCloseButton: false,
                            showCancelButton: false,
                            confirmButtonText: response.redirect ? 'Go to Login' : 'Return to Signup'
                        }).then((result) => {
                            if (result.isConfirmed && response.redirect) {
                                window.location.href = response.redirect;
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>