<?php

$dataKey  = 'm@7370m@73 #!1_2_3*4*%678 c1@v35@m';
$method = 'aes-256-cbc';
$iv = base64_decode("C9fBxl1EWtYTL1/M8jfstw==");

 $strHide = function ($valor) use ($method, $dataKey, $iv) {
     return openssl_encrypt ($valor, $method, $dataKey, false, $iv);
 };

 $strShow = function ($valor) use ($method, $dataKey, $iv) {
     $encrypted_data = base64_decode($valor);
     return openssl_decrypt($valor, $method, $dataKey, false, $iv);
 };

 $getIV = function () use ($method) {
     return base64_encode(openssl_random_pseudo_bytes(openssl_cipher_iv_length($method)));
 };
?>

    