<?php
ob_start();
session_start();
include("../config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $_SESSION['Transport_Price'] = $_POST['transport'];

    header('Location: ../pages/card.php');
    ob_end_clean();
    exit();
}

header('Location: ../pages/avcard.php');
exit();
