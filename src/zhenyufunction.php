<?php
    session_start();
    header('Content-Type: application/json');

    $Order_Num = $_SESSION['Order_Num'];
    echo $Order_Num;
?>