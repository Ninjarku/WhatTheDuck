<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>
    <head>     
        <?php
        session_start();
        session_unset();
        ?>
        <meta charset="UTF-8">
        <title>Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
              integrity=
              "sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
              crossorigin="anonymous"> 
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    </head>
    <body>
        <main class="container">
            <?php

            function sanitize_input($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }

            function authenticateUser() {
                global $admin_username, $errorMsg, $success;

                if (empty($_POST["admin_username"])) {
                    $errorMsg .= "Username is required.<br>";
                    $success = false;
                } else {
                    $admin_username = sanitize_input($_POST["admin_username"]);
                    if (!filter_var($admin_username, FILTER_SANITIZE_STRING)) {
                        $errorMsg .= "Invalid username format.<br>";
                        $success = false;
                    }
                }

                if (empty($_POST["admin_pass"])) {
                    $success = false;
                } else {
                    $admin_pw = $_POST["admin_pass"];
                }

                $config = parse_ini_file('/var/www/private/db-config.ini');
                $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

                if ($conn->connect_error) {
                    $errorMsg = "Connection failed: " . $conn->connect_error;
                    $success = false;
                } else {
                    $stmt = $conn->prepare("SELECT * FROM User WHERE Username=?");
                    $stmt->bind_param("s", $admin_username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Debugging logs
                    error_log("SQL Query executed.");
                    error_log("Number of rows: " . $result->num_rows);

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $db_username = $row["Username"];
                        $db_password = $row["Password"];
                        $admin_id = $row["User_ID"];

                        if (password_verify($admin_pw, $db_password)) {
                            $_SESSION["admin_login"] = "success";
                            $_SESSION["admin_username"] = $admin_username;
                            $_SESSION["userid"] = $admin_id;
                            $_SESSION["cust_id"] = $admin_id;

                            $success = true;
                        } else {
                            $errorMsg = "Invalid username or password.";
                            $success = false;
                        }
                    } else {
                        $errorMsg = "Invalid username or password.";
                        $success = false;
                    }

                    $stmt->close();
                }

                $conn->close();

                if ($success) {
                    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Login successful!',
            text: 'Welcome back, " . htmlspecialchars($admin_username) . "',
            showCloseButton: false,
            showCancelButton: false,
            confirmButtonText: 'Return to Home',
        }).then((result) => { 
            if (result.isConfirmed) {
                window.location.href = 'admin_index.php';
            }
        });
        </script>";
                } else {
                    $_SESSION["admin_username"] = "";
                    $_SESSION["admin_login"] = "failed";
                    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Login failed! Please try again.', 
            showCloseButton: false,
            showCancelButton: false,
            confirmButtonText: 'Return to Login',
        }).then((result) => { 
            if (result.isConfirmed) {
                window.location.href = 'Login.php';
            }
        });
        </script>";
                }

            }

            $errorMsg = "";
            $success = true;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                authenticateUser();
            }
            ?>
            <!--//            $recaptchaResponse = $_POST['g-recaptcha-response'];
            //$secretKey = "6LdqM0QlAAAAAMNppZCq8d33bRfmWyi6Dui4Wp3d";
            //$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$recaptchaResponse);
            //$responseData = json_decode($verifyResponse);
            //if ($responseData->success) {
            //    // User passed reCAPTCHA verification, proceed with form submission
            //    
            //                $success = true;
            //} else {
            //    // User failed reCAPTCHA verification, show error message
            //    $success = false;
            //    
            //}-->
            <!--   authenticateUser();-->

        </main>
    </body>
</html>
