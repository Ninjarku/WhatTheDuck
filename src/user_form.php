<?php
require 'jwt/jwt_cookie.php';
$decodedToken = checkAuthentication('IT Admin');
include_once "includes/navbar.php";


$Form_Type = isset($_GET['Form_Type']) ? intval($_GET['Form_Type']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Initialize an empty user array for adding a new user
$user = [
    'User_ID' => '',
    'Username' => '',
    'Password' => '',
    'Email' => '',
    'Mobile_Number' => '',
    'Billing_Address' => '',
    'Gender' => '',
    'DOB' => '',
    'User_Type' => '',
    'Account_Active' => 0
];

// Include your database connection here
if ($Form_Type == 1 && $action === 'editUser') {
    $User_ID = isset($_GET['User_ID']) ? intval($_GET['User_ID']) : 0;
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch user data for editing
    $stmt = $conn->prepare("SELECT * FROM User WHERE User_ID = ?");
    $stmt->bind_param("i", $User_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $Form_Type == 1 ? 'Edit User' : 'Add User'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- START OF THE LINK -->
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <!-- jQuery -->
    <script src="js/jquery-3.5.1.js" type="text/javascript"></script>
    <!--Bootstrap JS-->
    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
        integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm" crossorigin="anonymous">
        </script>
    <!-- DataTables JS -->
    <script defer src="js/datatables.min.js" type="text/javascript"></script>
    <!-- DataTables CSS -->
    <link href="css/datatables.min.css" rel="stylesheet" type="text/css" />
    <!-- FontAwesome for Icons -->
    <script src="https://kit.fontawesome.com/70ab820747.js" crossorigin="anonymous"></script>
    <!-- SweetAlert2 for Popups -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- END OF THE LINK -->
</head>

<body>
    <div class="container">
        <h2><?php echo $Form_Type == 1 ? 'Edit User' : 'Add User'; ?></h2>
        <form id="product-form" method="post" action="process_product.php?action=<?php echo $Form_Type == 1 ? 'editUser' : 'addUser'; ?>">
            <input type="hidden" name="User_ID" value="<?php echo htmlspecialchars($user['User_ID']); ?>">

            <div class="form-group">
                <label for="Username">Username:</label>
                <input type="text" class="form-control" id="Username" name="Username" value="<?php echo htmlspecialchars($user['Username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="Email">Email:</label>
                <input type="email" class="form-control" id="Email" name="Email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="Mobile_Number">Mobile Number:</label>
                <input type="text" class="form-control" id="Mobile_Number" name="Mobile_Number" maxlength="8" value="<?php echo htmlspecialchars($user['Mobile_Number']); ?>" required>
            </div>
            <div class="form-group">
                <label for="Billing_Address">Billing Address:</label>
                <input type="text" class="form-control" id="Billing_Address" name="Billing_Address" value="<?php echo htmlspecialchars($user['Billing_Address']); ?>">
            </div>
            <div class="form-group">
                <label for="Gender">Gender:</label>
                <select class="form-control" id="Gender" name="Gender" required>
                    <option value="Male" <?php echo $user['Gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo $user['Gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo $user['Gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="DOB">Date of Birth:</label>
                <input type="date" class="form-control" id="DOB" name="DOB"
                    value="<?php echo htmlspecialchars($user['DOB']); ?>" required>
            </div>
            <div class="form-group">
                <label for="User_Type">User Type:</label>
                <select class="form-control" id="User_Type" name="User_Type" required>
                    <option value="IT Admin" <?php echo $user['User_Type'] == 'IT Admin' ? 'selected' : ''; ?>>IT Admin</option>
                    <option value="Sales Admin" <?php echo $user['User_Type'] == 'Sales Admin' ? 'selected' : ''; ?>>Sales Admin</option>
                    <option value="Customer" <?php echo $user['User_Type'] == 'Customer' ? 'selected' : ''; ?>>Customer</option>
                </select>
            </div>
            <?php if ($User_ID == 0): // Show password fields only when adding a new user ?>
                <div class="form-group">
                    <label for="Password">Password:</label>
                    <input type="password" class="form-control" id="Password" name="Password" required>
                </div>
            <?php endif; ?>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="Account_Active" name="Account_Active" value="1" <?php echo $user['Account_Active'] == 1 ? 'checked' : ''; ?>>
                <label class="form-check-label" for="Account_Active">Account Active</label>
            </div>

            <button type="submit" class="btn btn-primary"><?php echo $Form_Type == 1 ? 'Update' : 'Add'; ?>User</button>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            $("#user-form").submit(function (event) {
                event.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr("action"),
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        Swal.fire({
                            icon: response.icon,
                            title: response.title,
                            text: response.message,
                            showCloseButton: false,
                            showCancelButton: false,
                            confirmButtonText: response.redirect ? 'OK' : 'OK'
                        }).then((result) => {
                            if (result.isConfirmed && response.redirect) {
                                window.location.href = response.redirect;
                            }
                        });
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'There was a problem with the request. Please try again.',
                            showCloseButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>

<?php include "includes/footer.php"; ?>
    
</body>
</html>