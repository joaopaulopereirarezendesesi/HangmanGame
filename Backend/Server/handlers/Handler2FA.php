<?php

namespace handlers;

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

    public function generateSecretImage(): void
    {
        try {
            $secretKey = $this->tfa->createSecret();
            $qrCodeImageData = $this->tfa->getQRCodeImageAsDataUri('HangmanGame', $secretKey);

            $base64Image = explode(',', $qrCodeImageData)[1];

            Utils::jsonResponse([
                "secret" => $secretKey,
                "image" => $base64Image,
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