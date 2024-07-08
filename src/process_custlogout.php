<?php
require '/var/www/html/jwt/jwt_cookie.php';

session_start();
session_destroy();

try {
        $jwt = getJWTFromCookie();
        if ($jwt) {
                $decodedToken = validateJWT($jwt, "/var/www/private/public.pem");
                if ($decodedToken) {
                        blacklistToken($jwt);
                }
        }
        unsetJWTInCookie();
} catch (Exception $e) {
        // Log the error if needed, or handle as necessary
}

header("Location: index.php");
exit();
?>
<!DOCTYPE html>
<html>

<head>
        <meta charset="UTF-8">
        <title>Logout</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
                integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
                crossorigin="anonymous">
        <link rel="stylesheet" href="css/main.css">
        <!-- jQuery -->
        <script defer src="https://code.jquery.com/jquery-3.4.1.min.js"
                integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <!-- Bootstrap JS -->
        <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
                integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
                crossorigin="anonymous"></script>
</head>

<body>
</body>

</html>