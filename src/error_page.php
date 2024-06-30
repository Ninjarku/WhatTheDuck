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
    <h1 class="text-center">ERROR!!!!!</h1>
    <p class="text-center"><?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php
    $error_id = isset($_GET['error_id']) ? intval($_GET['error_id']) : -1;
    if ($error_id == 0) {
        $button_text = "Back to Admin Login";
        $button_link = "admin_login.php";
    } else {
        $button_text = "Back to Admin Index";
        $button_link = "admin_index.php";
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
