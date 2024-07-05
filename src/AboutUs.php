<?php include 'includes/navbar.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About us!</title>
    <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
              integrity=
              "sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
              crossorigin="anonymous">
    <link rel="stylesheet" href="css/main.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            background-color: #fff5cc;
            font-family: 'Comic Neue', cursive;
        }

        .page-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        footer {
            margin-top: auto;
        }

        .main-container {
            border-radius: 5px;
        }

        .page-content {
            font-family: Arial, Helvetica, sans-serif;
        }

        .page-header {
            text-align: center;
        }

    </style>
</head>
<body>
    <div class="page-container">
        <div class="container my-1  p-5 main-container">
            <header>
                <div class="page-header">
                    <h1>About us</h1>
                </div>
            </header>
            <main>
                <div class="page-content p-3 pt-2">
                    <p class="my-2">
                        Welcome to WhatTheDuck, your ultimate destination for all things duck-themed! 
                        Whether you're a passionate collector or just looking to add a bit of whimsy to your life, 
                        we've got you covered with a wide range of delightful duck products.
                    </p>
                    <p class="my-2">
                       At WhatTheDuck, we believe that life is better with a little fun and a lot of ducks. 
                       Our journey began with a simple love for these charming creatures and a desire to share that love with the world. 
                       From the humble beginnings of a small online shop, we have grown into a beloved destination for duck enthusiasts everywhere.
                    </p class="my-2">
                    <p>
                       We offer an extensive selection of duck-themed items, from adorable plush toys and quirky home decor to stylish apparel and unique accessories. 
                       Each product is carefully selected to ensure it brings joy and a touch of ducky delight to your life.
                    </p>
                    <p class="my-2">
                        Quality and customer satisfaction are at the heart of everything we do. 
                        We are dedicated to providing top-notch products and exceptional service. 
                        Our team works tirelessly to ensure that every item meets our high standards of quality and cuteness.
                    </p>
                </div>
            </main>
        </div>
        <?php         
            include 'includes/footer.php';
        ?>
    </div>
</body>
</html>