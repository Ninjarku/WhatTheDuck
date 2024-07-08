<?php
session_start();
include "includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <link rel="stylesheet" 
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" 
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" 
          crossorigin="anonymous">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #fff5cc; /* Light yellow background */
        }
        .container {
            margin-top: 20px;
        }
        .otp-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            margin: 10px auto;
            background-color: #fff;
            max-width: 400px;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
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
        .btn:disabled {
            background-color: #8a8a8a;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="password-card">
            <h1 class="text-center">Reset Your Password</h1>
            <form action="process_resetpassword.php" method="post">
                <div class="form-group">
                    <label for="newPassword">New Password:</label>
                    <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
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
                <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Reset Password</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script defer 
            src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js" 
            integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm" 
            crossorigin="anonymous"></script>

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
            var pwd = $("#newPassword").val();
            var pwdconfirm = $("#confirmPassword").val();
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
                $('#submit-btn').prop('disabled', false);
            }

            else {
                $('#submit-btn').prop('disabled', true);
            }
        }

        $(document).ready(function () {
            $("#newPassword").on("input", function () {
                const password = $(this).val();
                checkPasswords(password);
            });

            $("#confirmPassword").on("input", function () {
                const password = $("#newPassword").val();
                checkPasswords(password);
            });
                
        });
    </script>
</body>
</html>

