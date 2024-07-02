<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>What The Duck</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  

    <!-- START OF THE LINK -->
    <!-- Bootstrap CSS -->
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
          crossorigin="anonymous"> 
    <!-- jQuery -->
    <script src="js/jquery-3.5.1.js" type="text/javascript"></script>
    <!--Bootstrap JS-->
    <script defer
            src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"
            integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm"
            crossorigin="anonymous">
    </script> 
     <!-- FontAwesome for Icons -->
    <script src="https://kit.fontawesome.com/70ab820747.js" crossorigin="anonymous"></script>
   <!-- END OF THE LINK -->

</head>

<body>
<?php include "includes/navbar.php"; ?>

<div class="container">
    <br>
    <?php
    $error_id = isset($_GET['error_id']) ? intval($_GET['error_id']) : -1;

    $title = $error_id === -1 ? "SUCCESS!!!!!" : "ERROR!!!!!";
    echo "<h1 class='text-center'>{$title}</h1>";
    ?>
    <p class="text-center"><?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php
    $error_redirects = [
        0 => ["Back to Admin Login", "Login.php"],
        1 => ["Back to Admin Index", "admin_index.php"]
        2 => ["Back to User Form", "user_form.php"],
        3 => ["Back to Product Index", "product_index.php"],
        4 => ["Back to Product Form", "product_form.php"],
    ];

    if (array_key_exists($error_id, $error_redirects)) {
        list($button_text, $button_link) = $error_redirects[$error_id];
    } else {
        $button_text = $error_id === -1 ? "Back to Home" : "Back to Home";
        $button_link = "index.php";
    }
    ?>
    <button id="btnBack" class="btn btn-primary" onclick="window.location.href='<?php echo $button_link; ?>'">
        <i class='fas fa-arrow-left' style="color:white;"></i> <?php echo $button_text; ?>
    </button>
    
    <br><br>
</div>

<?php include "includes/footer.php"; ?>
</body>
</html>
