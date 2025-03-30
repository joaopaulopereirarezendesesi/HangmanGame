<?php

namespace core;

use tools\Utils;
use Exception;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;

class Handler2FA
{
    private TwoFactorAuth $tfa;

    public function __construct()
    {
        $this->tfa = new TwoFactorAuth(new BaconQrCodeProvider());
    }

    public function generateSecretImage(string $userId): void
    {
        try {
            $secret = $this->tfa->createSecret();
            $imageData = $this->tfa->getQRCodeImageAsDataUri('HangmanGame', $secret);
    
            $imageBase64 = explode(',', $imageData)[1];
    
            Utils::jsonResponse([
                "secret" => $secret,
                "image" => $imageBase64,
            ]); 
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "coreErroHandler2FA-generateSecretImage" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }
     
}