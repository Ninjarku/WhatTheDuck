<?php
session_start();
include "includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>WhatTheDuck - My Account</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
          crossorigin="anonymous">
    <link rel="stylesheet" href="css/main.css">
    <!-- jQuery -->
    <script defer
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script defer
            src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
            integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body, html {
            font-family: 'Comic Neue', cursive;
            background-color: #fff5cc;
            color: black;
        }
        .navbar {
            background-color: #ffcc00;
        }
        .navbar-brand img {
            border-radius: 50%;
        }
        .nav-item .nav-link, .login-link, .cart-link {
            color: black !important;
            font-weight: bold;
            display: inline-block;
            padding: 10px 15px;
        }
        .nav-item .nav-link:hover, .login-link:hover, .cart-link:hover {
            color: #fff !important;
            background-color: #ff6347;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out;
        }
        .profile-sidebar {
            background-color: #ffcc00;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .profile-sidebar .nav-link {
            color: black;
        }
        .profile-sidebar .nav-link:hover {
            background-color: #ff6347;
            color: white;
            border-radius: 5px;
        }
        .profile-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .profile-content h1 {
            color: black;
        }
        .profile-content .form-group label {
            font-weight: bold;
        }
        .profile-content .form-group input, .profile-content .form-group select {
            border: 1px solid #ddd;
            border-radius: 5px;
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
        .profile-img-container {
            text-align: center;
            margin-top: 20px;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .form-container {
            flex: 1;
        }
        .image-container {
            margin-left: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            margin-top: 50px;
            margin-bottom: 50px;
        }
    </style>
    <script>
        $(document).ready(function () {
            $("#profile-form").on("submit", function (event) {
                event.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: "POST",
                    url: "process_myaccount.php",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        Swal.fire({
                            icon: response.icon,
                            title: response.title,
                            text: response.message,
                            showCloseButton: false,
                            showCancelButton: false,
                            confirmButtonText: response.redirect ? 'Return to MyAccount' : 'OK'
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
</head>
<body>
    <?php
    if ($_SESSION["cust_login"] == "success") {
        $success = true;
        global $username, $birthday, $email, $mobile, $address, $gender, $profile_image;
        $config = parse_ini_file('/var/www/private/db-config.ini');
        $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
        if ($conn->connect_error) {
            $errorMsg = "Connection failed: " . $conn->connect_error;
            $success = false;
        } else {
            // Prepare the statement:
            $stmt = $conn->prepare("SELECT * FROM User WHERE Username=?");
            // Bind & execute the query statement:
            $stmt->bind_param("s", $_SESSION["cust_username"]);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $username = $row["Username"];
                $birthdaystr = $row["DOB"];
                $birthday = date('Y-m-d', strtotime($birthdaystr));
                $email = $row["Email"];
                $mobile = $row["Mobile_Number"];
                $address = $row["Billing_Address"];
                $gender = $row["Gender"];
                $profile_image = $row["Profile_Image"];
            } else {
                echo "User not found";
            }
        }
        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="profile-sidebar">
                        <h2>My Account</h2>
                        <nav class="nav flex-column">
                            <a class="nav-link active" href="#">Profile</a>
                            <a class="nav-link" href="ForgetPasssword.php">Change Password</a>
                        </nav>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="profile-content">
                        <form id="profile-form" method="post" enctype="multipart/form-data" class="d-flex w-100">
                            <div class="form-container">
                                <h1>My Profile</h1>
                                <p>Manage and protect your account</p>
                                <div class="form-group">
                                    <label for="username_input">Username</label>
                                    <input type="text" id="username_input" name="username_input" value="<?php echo $username; ?>" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="email_input">Email</label>
                                    <input type="email" id="email_input" name="email_input" value="<?php echo $email; ?>" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="mobile_input">Mobile Number</label>
                                    <input type="text" id="mobile_input" name="mobile_input" value="<?php echo $mobile; ?>" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="address_input">Address</label>
                                    <input type="text" id="address_input" name="address_input" value="<?php echo $address; ?>" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="gender_input">Gender</label>
                                    <select id="gender_input" name="gender_input" class="form-control">
                                        <option value="Male" <?php if ($gender == 'Male') echo 'selected'; ?>>Male</option>
                                        <option value="Female" <?php if ($gender == 'Female') echo 'selected'; ?>>Female</option>
                                        <option value="Other" <?php if ($gender == 'Other') echo 'selected'; ?>>Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="birthday_input">Date of Birth</label>
                                    <input type="date" id="birthday_input" name="birthday_input" value="<?php echo $birthday; ?>" class="form-control">
                                </div>
                                <button class="btn btn-primary mt-3" type="submit" name="save_btn">Save</button>
                            </div>
                            <div class="image-container">
                                <h3>Profile Picture</h3>
                                <div class="profile-img-container">
                                    <?php
                                    if ($profile_image) {
                                        echo '<img src="data:image/jpeg;base64,' . base64_encode($profile_image) . '" alt="Profile Image" class="profile-img">';
                                    } else {
                                        echo '<img src="images/default_profile.jpg" alt="Profile Image" class="profile-img">';
                                    }
                                    ?>
                                    <input type="file" name="profile_image" accept="image/*" class="form-control-file mt-2">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $conn->close();
        include "includes/footer.php";
    } else {
        echo "<br>";
        echo "<main class='container'>";
        echo "<h1>Oops!</h1>";
        echo "<h4>Please login to view the page.</h4>";
        echo "<a href='Login.php' class='btn btn-warning' style='color:white;text-decoration: none;'>Go to Login</a>";
        echo "</main>";
        echo "<br>";
        include "includes/footer.php";
    }
    ?>
</body>
</html>
