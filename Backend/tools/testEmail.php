<?php

require './helpers.php';

use tools\Utils;

$utils = new Utils();

$to = 'joaopp.rezende@gmail.com';
$subject = 'Test Email';
$body = '<h1>This is a test email</h1><img src="cid:image1">';
$imagePath = './../assets/emailBackgroundImage.png';

$utils->sendEmailWithInlineImage($to, $subject, $body, $imagePath);
echo "Email enviado com sucesso!";