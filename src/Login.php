<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Project/PHP/PHPProject.php to edit this template
-->
<html>
    <head>
        <title>WhatTheDuck - Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
              integrity=
              "sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
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
            a:hover {
                color: #ff6347;
                text-decoration: none;
            }
        </style>
        <?php
// Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Retrieve the user's input
            $cust_username = $_POST['cust_username'];
            $cust_password = $_POST['cust_pass'];

            // Sanitize the input
            $cust_username = filter_var($cust_username, FILTER_SANITIZE_STRING);
            $cust_password = filter_var($cust_password, FILTER_SANITIZE_STRING);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Query the database to check if the username and password combination exists
            $sql = "SELECT * FROM users WHERE username='$cust_username' AND password='$cust_password'";
            $result = $conn->query($sql);

            // If the username and password are correct, create a session and redirect the user
            if ($result->num_rows > 0) {
                session_start();
                $_SESSION['username'] = $username;
                header('Location: index.php');
                exit();
            }
            // If the username and password are incorrect, display an error message
            else {
                echo "Invalid username or password";
            }

            // Close the database connection
            $conn->close();
        }
        ?>

    </head>

    <body style="background-color:#fff5cc;">
        <?php include 'includes/navbar.php'; ?>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                    <h1>Login</h1>
                    <div class="login-form">
                        <form action="process_custlogin.php" id="cust-login" method="post" accept-charset="UTF-8">
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
