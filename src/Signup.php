<!DOCTYPE html>
<html lang="en">
<head>
    <title>WhatTheDuck - Sign up</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
          crossorigin="anonymous">
    <link rel="stylesheet" href="css/main.css">
    <!--jQuery-->
    <script defer
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous">
    </script>
    <!--Bootstrap JS-->
    <script defer
            src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
            integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
            crossorigin="anonymous">
    </script>
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
        a:hover {
            color: #ff6347;
            text-decoration: none;
        }
    </style>
</head>
<body style="background-color:#fff5cc;">
<!--    TODO: Toggle password visiblity-->
    <?php include 'includes/navbar.php'; ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5 col-xl-5">
                <h1>Sign Up</h1>
                <?php
                session_start();
                if (isset($_SESSION['signup_error'])) {
                    // If so, display the error message and then unset the session variable
                    echo '<p>' . $_SESSION['signup_error'] . '</p>';
                }
                ?>
                <div class="signup-form">
                    <form action="process_custsignup.php" id="cust-signup" method="post" accept-charset="UTF-8">
                        <div class="form-group">
                            <label for="signup_username">Username:</label>
                            <input class="form-control" type="text" id="signup_username"
                                   maxlength="45" required name="signup_username" placeholder="Enter username">
                        </div>
                        <div class="form-group">
                            <label for="signup_email">Email:</label>
                            <input class="form-control" type="email" id="signup_email" required name="signup_email" placeholder="Enter email">
                        </div>
                        <div class="form-group">
                            <label for="signup_mobile_number">Mobile Number:</label>
                            <input class="form-control" type="text" id="signup_mobile_number"
                                   maxlength="8" required name="signup_mobile_number" placeholder="Enter mobile number">
                        </div>
                        <div class="form-group">
                            <label for="signup_birthday">Date of Birth:</label>
                            <input class="form-control" type="date" required name="signup_birthday" id="signup_birthday">
                        </div>
                        <div class="form-group">
                            <label for="signup_pwd">Password:</label>
                            <input class="form-control" type="password" id="signup_pwd"
                                   required name="signup_pwd" placeholder="Enter password">
                        </div>
                        <div class="form-group">
                            <label for="signup_pwdconfirm">Confirm Password:</label>
                            <input class="form-control" type="password" id="signup_pwdconfirm"
                                   required name="signup_pwdconfirm" placeholder="Confirm password">
                        </div>
                        <div class="form-check">
                            <label>
                                <input required type="checkbox" name="agree">
                                Agree to <a href="T&C.php">terms and conditions</a>.
                            </label>
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
</body>
</html>