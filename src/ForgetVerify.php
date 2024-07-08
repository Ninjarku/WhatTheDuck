<?php
session_start();
include "includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatTheDuck - Verify OTP</title>
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
        /* .otp-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            margin: 10px auto;
            background-color: #fff;
            max-width: 400px;
        } */
        .navbar {
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="otp-card">
            <h1 class="text-center">Verify OTP</h1>
            <p>Please enter the OTP sent to your email address.</p>
            <form action="process_verifyforgetotp.php" method="post">
                <div class="form-group">
                    <label for="otp">OTP:</label>
                    <input type="text" class="form-control" id="otp" name="otp" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary">Verify</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script defer 
            src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js" 
            integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm" 
            crossorigin="anonymous"></script>
</body>
</html>
