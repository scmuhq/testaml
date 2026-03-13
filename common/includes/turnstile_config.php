<?php
$TURNSTILE_SITE_KEY = '1x00000000000000000000AA';
$TURNSTILE_SECRET_KEY = '1x0000000000000000000000000000000AA';

function verify_turnstile($token) {
    global $TURNSTILE_SECRET_KEY;
    if (empty($token)) return false;
    
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    $data = array(
        'secret' => $TURNSTILE_SECRET_KEY,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    );
    
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) return false;
    
    $result = json_decode($response, true);
    return isset($result['success']) && $result['success'] === true;
}
?>
