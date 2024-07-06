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
    <script src="https://kit.fontawesome.com/70ab820747.js" crossorigin="anonymous"></script>
    <script src="js/zxcvbn.js"></script>
    <style>
        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            box-sizing: border-box;
        }

        label {
            color: black;
        }

        body,
        .container,
        h1,
        p,
        a,
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
        .btn:disabled {
            background-color: #8a8a8a;
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
            .passwordWarning{
                color: red;
                text-align: center;
            }
            .passwordWarningGroup {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 5px;
                flex-direction: column;
            }

        .password-container {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .password-container input {
            flex: 1;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            cursor: pointer;
        }

        #password-strength {
            margin-top: 5px;
            color: black;
            text-align: left;
        }
    </style>
</head>

<body style="background-color:#fff5cc;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5 col-xl-5">
                <h1>Sign Up</h1>
                <?php
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
                            <div class="password-container">
                                <input class="form-control" type="password" id="signup_pwd" required name="signup_pwd"
                                    placeholder="Enter password">
                                <span class="toggle-password"><i class="fas fa-eye"></i></span>
                            </div>
                            <div id="password-strength"></div>
                        </div>
                        <div class="form-group">
                            <label for="signup_pwdconfirm">Confirm Password:</label>
                            <input class="form-control" type="password" id="signup_pwdconfirm" required
                                name="signup_pwdconfirm" placeholder="Confirm password">
                        </div>
                            <div class="passwordWarning">
                                <small id="passwordLengthWarning" class="passwordWarningGroup" style="display: none;">
                                    Password must be at least 12 characters long.
                                </small>
                                <small id="passwordComplexityWarning" class="passwordWarningGroup" style="display: none;">
                                    Password must comprise of uppercase, lowercase, and numbers.
                                </small>
                                <small id="confirmPasswordWarning" class="passwordWarningGroup" style="display: none;">
                                    Passwords do not match.
                                </small>
                            </div>
                        <div class="form-group">
                            <button class="btn btn-primary" style="width: 100%;" type="submit" id="signup_btn" disabled>Submit</button>
                        </div>
                    </form>
                </div>
                <p>Have an account? <a href="Login.php">Login Now!</a></p>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script>
            function checkPasswordLength(password) {
                if (password.length >= 12 && password.length <= 128) {
                    $('#passwordLengthWarning').hide();
                    return true
                }
                else {
                    $('#passwordLengthWarning').show();
                    return false
                }
            }

            function checkPasswordComplexity(password) {
                const complexityRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/;
                if (complexityRegex.test(password)) {
                    $('#passwordComplexityWarning').hide();
                    return true
                }
                else {
                    $('#passwordComplexityWarning').show();
                    return false
                }
            }
            
            function checkBothPasswords() {
                var pwd = $("#signup_pwd").val();
                var pwdconfirm = $("#signup_pwdconfirm").val();
                if (pwd == pwdconfirm) {
                    $('#confirmPasswordWarning').hide();
                    return true
                }
                else {
                    $('#confirmPasswordWarning').show();
                    return false
                }
            }

            function checkPasswords(password) {
                var lengthBool = checkPasswordLength(password);
                var complexityBool = checkPasswordComplexity(password);
                var confirmPasswordBool = checkBothPasswords();
                if (lengthBool && complexityBool && confirmPasswordBool) {
                    $('#signup_btn').prop('disabled', false);
                }

                else {
                    $('#signup_btn').prop('disabled', true);
                }
            }

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

            $('.toggle-password').on('click', function () {
                var passwordField = $('#signup_pwd');
                var passwordFieldType = passwordField.attr('type');
                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).html('<i class="fas fa-eye-slash"></i>');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).html('<i class="fas fa-eye"></i>');
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
                            html: response.message,
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

            $("#signup_pwd").on("input", function () {
                const password = $(this).val();
                checkPasswords(password);
            });

            $("#signup_pwdconfirm").on("input", function () {
                const password = $("#signup_pwd").val();
                checkPasswords(password);
            });
                
        });
    </script>
</body>

</html>