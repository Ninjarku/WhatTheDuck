<noscript style="color:white;background-color:black;width: 100%;display: block;">This course portal requires JavaScript to verify your identity. Please enable JavaScript to access the course.</noscript>
<?php
session_start();
?>
<script src="/js/jquery-3.5.1.js" type="text/javascript"></script>
<script src="https://kit.fontawesome.com/70ab820747.js" crossorigin="anonymous"></script>
<script src="/js/navbar-active-btn.js" type="text/javascript"></script>
<link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@400;700&display=swap" rel="stylesheet">
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
    .fas, .cartcount {
        color: black;
    }
    .sidenav {
        height: 100%;
        width: 0;
        position: fixed;
        z-index: 3;
        top: 0;
        right: 0;
        background-color: #ffcc00;
        overflow-x: hidden;
        padding-top: 60px;
        transition: 0.5s ease;
    }
    .sidenav a {
        padding: 8px 8px 8px 32px;
        text-decoration: none;
        font-size: 25px;
        color: black;
        display: block;
        transition: 0.3s;
    }
    .sidenav a:hover {
        color: #fff;
        background-color: #ff6347;
        border-radius: 5px;
    }
    .sidenav .closebtn {
        position: absolute;
        top: 0;
        right: 25px;
        font-size: 36px;
        margin-left: 50px;
    }
    #overlay {
        display: none;
        height: 100%;
        width: 100%;
        position: fixed;
        z-index: 2;
        background-color: rgba(0,0,0,0.6);
    }
    @media screen and (max-height: 450px) {
        .sidenav {
            padding-top: 15px;
        }
        .sidenav a {
            font-size: 18px;
        }
    }
</style>

<div id="overlay"></div>
<nav class="navbar navbar-expand-sm navbar-dark" style="justify-content: space-between;width:100%;padding-top: 2rem;">
    <div class="col col-lg-7 col-sm-6 position-static d-lg-block">
        <a class="navbar-brand" href="index.php">
            <img src="images/logo_icon2.jpg" alt="Logo icon" width="100%" height="100"/>
        </a>
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="OurRooms.php">Our Rooms</a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link" href="AboutUs.php">About Us</a>
            </li>
            <?php
            if (isset($_SESSION["cust_login"]) && $_SESSION["cust_login"] == "success") {
                ?>
                <li class="nav-item">
                    <a class="nav-link login-link" onclick="openNav()" style="cursor:pointer;"><?php echo $_SESSION['cust_username']; ?></a>
                </li>
                <?php
            } else {
                ?>
                <li class="nav-item">
                    <a class="nav-link login-link" href="Login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link login-link" href="Signup.php">Sign Up</a>
                </li>
                <?php
            }
            ?>
            <li class="nav-item">
                <a class="nav-link cart-link" href="cart.php">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cartinfo d-inline-block"></span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<div id="AccountSidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="MyAccount.php">My Account</a>
    <a href="ViewReservations.php">View Reservation</a>
    <a href="process_custlogout.php">Logout</a>
</div>
<script>
    function openNav() {
        $("#AccountSidenav").css("width", "350px");
        $("#overlay").css("display", "block");
    }
    function closeNav() {
        $("#AccountSidenav").css("width", "0");
        $("#overlay").css("display", "none");
    }
</script>