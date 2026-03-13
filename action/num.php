<?php
ob_start();
session_start();
include("../config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['phone_number'] = $_POST['phone_number'];
}

?>