<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Project/PHP/PHPProject.php to edit this template
-->
<html>
    <head>
        <title>What The Duck</title>
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
            .row{
                margin-top:20px;
            }
            #box{
                border: 1px solid black;
                border-radius: 20px;
                box-shadow: 5px 10px #888888;
                box-sizing: border-box;
                padding:40px;
            }
        </style>
<?php

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Retrieve the user's input
    $admin_username = $_POST['admin_username'];
    $admin_password = $_POST['admin_pass'];
    
    // Sanitize the input
    $admin_username = filter_var($admin_username, FILTER_SANITIZE_STRING);
    $admin_password = filter_var($admin_password, FILTER_SANITIZE_STRING);
    

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query the database to check if the username and password combination exists
    $sql = "SELECT * FROM users WHERE username='$admin_username' AND password='$admin_password'";
    $result = $conn->query($sql);

    // If the username and password are correct, create a session and redirect the user
    if ($result->num_rows > 0) {
        session_start();
        $_SESSION['username'] = $username;
        header('Location: admin_index.php');
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

       <body style="background-color:black;">
                   <?php include 'includes/navbar.php'; ?>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                    <h1 style="color:white;">Admin Login</h1>
                    <div class="login-form">
                        <form action="process_staff_login.php" id="admin-login" method="post" accept-charset="UTF-8">
                            <div class="form-group" id="name-wrapper"> 
                                <input type="text" name="admin_username" id="admin_username" value="" class="form-control" placeholder="Username" required="">
                            </div>
                            <div class="form-group" id="password-wrapper"> 
                                <input type="password" name="admin_pass" id="admin_pass" value="" class="form-control" placeholder="Password" required="">
                            </div>
                            <input class="btn btn-primary" style="width: 100%;" type="submit" name="op" id="submit" value="Sign In" class="notranslate form-submit">
                        </form>
                    </div>
                </div>        
            </div>
        </div>
                   <?php include 'includes/footer.php'; ?>
    </body>
</html>
