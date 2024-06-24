<?php
require "jwt_cookie.php"
require "jwt_blacklist.php"


// Example authentication check
function authenticatio_check(){
    try {
        $publicKeyPath = '/path/to/public/public.key';
        $jwt = getJWTFromCookie();

        if ($jwt) {
            $decodedToken = validateJWT($jwt, $publicKeyPath);
            if ($decodedToken) {
                echo 'Valid JWT: ', json_encode($decodedToken);
                if ($decodedToken['rol'] == "SalesAdmin" or $decodedToken['rol'] == "ITAdmin"){
                    // Permit access to salesadmin and itadmin stuff
                }
                else {
                    // Check if its customer has access 
                    
                }
            } else {
                echo 'Invalid JWT.';
            }
        } else {
            echo 'No JWT found in cookie.';
        }
    } catch (Exception $e) {
        echo 'Error: ', $e->getMessage(), "\n";
    }    
}



// Example Authentication
// After performing authentication check
$userId = "id queried from DB"
$privateKeyPath = '/path/to/private/private.key';

// Creates the cookie and sets it in the user's session
$jwt = generateJWT($userId, $privateKeyPath) 
setJWTInCookie($jwt)

// Removal of cookie
?>