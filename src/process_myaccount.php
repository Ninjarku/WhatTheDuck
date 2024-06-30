<!DOCTYPE html>
<html lang="en">
    <?php
    session_start();
    ?>
    <head>
        <title>My Account</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
              integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
              crossorigin="anonymous">
        <!-- jQuery -->
        <script defer
                src="https://code.jquery.com/jquery-3.4.1.min.js"
                integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
                crossorigin="anonymous">
        </script>
        <!-- Bootstrap JS -->
        <script defer
                src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
                integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
                crossorigin="anonymous">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        function sanitize_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $success = true;
        $errorMsg = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $cust_user = $_SESSION['cust_username'];
            $username = sanitize_input($_POST['username_input']);
            $email = sanitize_input($_POST["email_input"]);
            $mobile = sanitize_input($_POST["mobile_input"]);
            $address = sanitize_input($_POST["address_input"]);
            $gender = sanitize_input($_POST["gender_input"]);
            $birthday = sanitize_input($_POST['birthday_input']);
            $profile_image = null;

            // Database connection
            $config = parse_ini_file('/var/www/private/db-config.ini');
            $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);

            if ($conn->connect_error) {
                $errorMsg = "Connection failed: " . $conn->connect_error;
                $success = false;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMsg .= "Invalid email format.<br>";
                $success = false;
            }

            // Check if email already exists in the database for another user
            if ($success) {
                $stmt = $conn->prepare("SELECT Email FROM User WHERE Email = ? AND Username != ?");
                $stmt->bind_param("ss", $email, $cust_user);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $success = false;
                    $errorMsg .= "Email already in use.<br>";
                }
                $stmt->close();
            }

            // Handle profile image upload
            if ($success && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
                $imageData = file_get_contents($_FILES['profile_image']['tmp_name']);
                $profile_image = $imageData;
            }

            // Update user information if validation is successful
            if ($success) {
                if ($profile_image) {
                    $stmt = $conn->prepare("UPDATE User SET Username = ?, Email = ?, Mobile_Number = ?, Billing_Address = ?, Gender = ?, DOB = ?, Profile_Image = ? WHERE Username = ?");
                    $stmt->bind_param("ssssssss", $username, $email, $mobile, $address, $gender, $birthday, $profile_image, $cust_user);
                } else {
                    $stmt = $conn->prepare("UPDATE User SET Username = ?, Email = ?, Mobile_Number = ?, Billing_Address = ?, Gender = ?, DOB = ? WHERE Username = ?");
                    $stmt->bind_param("sssssss", $username, $email, $mobile, $address, $gender, $birthday, $cust_user);
                }

                if ($stmt->execute()) {
                    $_SESSION['cust_username'] = $username;  // Update session username if changed
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Profile updated successfully!',
                            showCloseButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'Return to MyAccount',
                        }).then((result) => { 
                            if (result.isConfirmed) {
                                window.location.href = 'MyAccount.php';
                            }
                        });
                      </script>";
                } else {
                    $success = false;
                    $errorMsg = "Update failed: " . $stmt->error;
                }
                $stmt->close();
            }

            // Display error message if update fails
            if (!$success) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Profile update failed!',
                        html: '{$errorMsg}',
                        showCloseButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'Return to MyAccount',
                    }).then((result) => { 
                        if (result.isConfirmed) {
                            window.location.href = 'MyAccount.php';
                        }
                    });
                  </script>";
            }

            $conn->close();
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid request!',
                    text: 'Please submit the form properly.',
                    showCloseButton: false,
                    showCancelButton: false,
                    confirmButtonText: 'Return to MyAccount',
                }).then((result) => { 
                    if (result.isConfirmed) {
                        window.location.href = 'MyAccount.php';
                    }
                });
              </script>";
        }
        ?>
    </body>
</html>