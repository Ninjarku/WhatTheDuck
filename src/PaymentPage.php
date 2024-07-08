<?php
session_start();

if (!isset($_SESSION["cust_login"]) || $_SESSION["cust_login"] !== "success") {
    header("Location: Login.php");
    exit();
}

$User_ID = $_SESSION['userid']; 

if (isset($_POST['selectedCartIds'])) {
    $cart_ids = $_POST['selectedCartIds'];

    $totalprice = 0;

    // Create database connection.
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $arrResult = [];
    // An array to hold the rows of data
    $rows = array();
    // Loop through each cart ID and retrieve the record from the database
    $cartitem = array(); 
    for ($x = 0; $x < sizeof($cart_ids); $x++) { 
        $stmt = $conn->prepare("SELECT c.Cart_ID, c.Quantity, c.Price, p.Product_Name, p.Product_Image 
                            FROM Cart c
                            JOIN Product p ON c.Product_ID = p.Product_ID 
                            WHERE c.Cart_ID = ? AND User_ID = ? ");
        $stmt->bind_param('i', $cart_ids[$x], $User_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $cartitem[] = $row;
            }
        }
    }

    $_SESSION['selectedCartIds'] = $cart_ids;
}else { 
    // Redirect back to cart if no items selected
    echo "<script> 
        window.location.href = 'cart.php';
    </script>";
    exit();
}

include 'includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatTheDuck - Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body,
        html {
            font-family: 'Comic Neue', cursive;
            background-color: #fff5cc;
            height: 100%;
            margin: 0;
            background-color: #fff5cc;
        }

        .container {
            margin-top: 20px;
        }

        .payment-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: left;
            padding: 20px;
            margin: 10px auto;
            background-color: #fff;
            max-width: 600px;
        }

        .navbar,
        .footer {
            margin-bottom: 20px;
        }

        #submitBtn:disabled {
            background-color: #8a8a8a;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="payment-card">
            <h1 class="text-center">Payment</h1>
            <form id="submit-payment" method="post">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="fullName">Full Name:</label>
                        <input type="text" class="form-control" id="fullName" name="fullName" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="phoneNumber">Phone Number:</label>
                        <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" pattern="\d{8}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Building, Street, and etc.:</label>
                    <input type="text" class="form-control" id="address" name="address" required>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="postalCode">Postal Code:</label>
                        <input type="text" class="form-control" id="postalCode" name="postalCode" pattern="\d{6}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="unitNo">Unit No (Optional):</label>
                        <input type="text" class="form-control" id="unitNo" name="unitNo">
                    </div>
                </div>
                <div class="form-group">
                    <label for="paymentMethod">Select Payment Method:</label>
                    <select class="form-control" id="paymentMethod" name="paymentMethod" required>
                        <option value="">Select...</option>
                        <option value="card">Credit/Debit Card</option>
                        <option value="cod">Cash on Delivery</option>
                    </select>
                </div>
                <div id="cardDetails" style="display: none;">
                    <div class="form-group">
                        <label for="cardName">Name on Card</label>
                        <input type="text" class="form-control" id="cardName" name="cardName"
                            placeholder="Enter name on card">
                    </div>
                    <div class="form-group">
                        <label for="cardNumber">Card Number:</label>
                        <input type="text" class="form-control" id="cardNumber" name="cardNumber" pattern="\d{16}"
                            placeholder="Enter 16-digit card number">
                        <small id="cardNumberError" class="form-text text-danger" style="display: none;"></small>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="expiryDate">Expiry Date:</label>
                            <input type="text" class="form-control" id="expiryDate" name="expiryDate"
                                pattern="\d{2}/\d{2}" placeholder="MM/YY">
                            <small id="expiryDateError" class="form-text text-danger" style="display: none;"></small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="cvv">CVV:</label>
                            <input type="text" class="form-control" id="cvv" name="cvv" pattern="\d{3}"
                                placeholder="Enter 3-digit CVV">
                            <small id="cvvError" class="form-text text-danger" style="display: none;"></small>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" id="submitBtn">Pay Now</button>
                <small id="cardPrompt" class="form-text text-danger" style="display: none;"></small>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
        integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
        crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            $('#paymentMethod').change(function () {
                if ($(this).val() === 'card') {
                    $('#cardPrompt').text('Please fill in your card details correctly.').show();
                    $('#submitBtn').prop('disabled', true);
                    $('#cardDetails').show();
                    $('#cardName').prop("required", true);
                    $('#cardNumber').prop("required", true);
                    $('#expiryDate').prop("required", true);
                    $('#cvv').prop("required", true);
                } else {
                    $('#cardPrompt').hide();
                    $('#submitBtn').prop('disabled', false);
                    $('#cardDetails').hide();
                    $('#cardName').prop("required", false);
                    $('#cardName').val('');
                    $('#cardNumber').prop("required", false);
                    $('#cardNumber').val('');
                    $('#expiryDate').prop("required", false);
                    $('#expiryDate').val('');
                    $('#cvv').prop("required", false);
                    $('#cvv').val('');
                }
            });

            function isDigits(str) {
                    return /^\d+$/.test(str);
                }

            function validateExpiryDate() {
                    const expiryDate = $('#expiryDate').val();
                    const expiryDateError = $('#expiryDateError');
                    const regex = /^(\d{2})\/(\d{2})$/;
                    
                    if (!regex.test(expiryDate)) {
                        expiryDateError.text('Invalid format. Use MM/YY.').show();
                        return false;
                    }
                    
                    const [, month, year] = expiryDate.match(regex);
                    const currentYear = new Date().getFullYear() % 100; 
                    const currentMonth = new Date().getMonth() + 1; 

                    if (month < 1 || month > 12) {
                        expiryDateError.text('Invalid month. Use MM between 01 and 12.').show();
                        return false;
                    }

                    if (year < currentYear || (year == currentYear && month < currentMonth)) {
                        expiryDateError.text('The card have expired.').show();
                        return false;
                    }

                    expiryDateError.hide();
                    return true;
                }

            function validateCardNumber(){
                const cardNumber = $('#cardNumber').val();
                const cardNumberError = $('#cardNumberError');

                if (cardNumber.length != 16 || !isDigits(cardNumber)) {
                    cardNumberError.text('Invalid format. Card number consists of 16 digits').show();
                    return false;
                }

                cardNumberError.hide();
                return true;
            }

            function validateCVV() {
                const cvv = $('#cvv').val();
                const cvvError = $('#cvvError');

                if (cvv.length != 3 || !isDigits(cvv)) {
                    cvvError.text('Invalid format. CVV consists of 3 digits').show();
                    return false;
                }

                cvvError.hide();
                return true;
            }
            
            function cardValidation(){
                var expiryBool = validateExpiryDate();
                var numberBool = validateCardNumber();
                var cvvBool = validateCVV();
                if (expiryBool && numberBool && cvvBool) {
                    $('#cardPrompt').hide();
                    $('#submitBtn').prop('disabled', false);
                }
                
                else {
                    $('#cardPrompt').text('Please fill in your card details correctly.').show();
                    $('#submitBtn').prop('disabled', true);
                }
            }

            $('#expiryDate').on('input', function () {
                cardValidation();
            });

            $('#cardNumber').on('input', function () {
                cardValidation();
            });

            $('#cvv').on('input', function () {
                cardValidation();
            });

            $("#submit-payment").on("submit", function (event) {
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "/process_payment.php",
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                        else {
                            Swal.fire({
                                icon: response.icon,
                                title: response.title,
                                icon: response.icon,
                                html: response.message,
                            })
                        }
                    }   
                });
            });
        });
    </script>
</body>

</html>