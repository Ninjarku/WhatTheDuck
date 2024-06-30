<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WhatTheDuck - Payment</title>
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
              integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
              crossorigin="anonymous">
        <style>
            body, html {
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
            .navbar, .footer {
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <?php include 'includes/navbar.php'; ?>

        <div class="container">
            <h1 class="text-center">Payment</h1>
            <div class="payment-card">
                <form action="payment_success.php" method="post">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="fullName">Full Name:</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phoneNumber">Phone Number:</label>
                            <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Building, Street, and etc.:</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="postalCode">Postal Code:</label>
                            <input type="text" class="form-control" id="postalCode" name="postalCode" required>
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
                            <input type="text" class="form-control" id="cardNumber" name="cardName" placeholder="Enter name on card">
                        </div>
                        <div class="form-group">
                            <label for="cardNumber">Card Number:</label>
                            <input type="text" class="form-control" id="cardNumber" name="cardNumber" pattern="\d{16}" placeholder="Enter 16-digit card number">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="expiryDate">Expiry Date:</label>
                                <input type="text" class="form-control" id="expiryDate" name="expiryDate" pattern="\d{2}/\d{2}" placeholder="MM/YY">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cvv">CVV:</label>
                                <input type="text" class="form-control" id="cvv" name="cvv" pattern="\d{3}" placeholder="Enter 3-digit CVV">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Pay Now</button>
                </form>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>

        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js" integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function () {
                $('#paymentMethod').change(function () {
                    if ($(this).val() === 'card') {
                        $('#cardDetails').show();
                    } else {
                        $('#cardDetails').hide();
                    }
                });
            });
        </script>
    </body>
</html>
