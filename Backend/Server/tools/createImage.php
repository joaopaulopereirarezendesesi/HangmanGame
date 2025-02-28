<?php
class ImageOverlay
{
    private $baseImage;
    private $overlayImage;
    private $fontPath;

    public function __construct($fontPath)
    {
        $this->fontPath = $fontPath;
    }

    public function loadBaseImage($path)
    {
        $this->baseImage = imagecreatefrompng($path);
        imagesavealpha($this->baseImage, true);
    }

    public function createOverlayImage($width, $height, $backgroundColor = [0, 0, 0, 127])
    {
        $this->overlayImage = imagecreatetruecolor($width, $height);
        imagealphablending($this->overlayImage, false);
        imagesavealpha($this->overlayImage, true);

        $bgColor = imagecolorallocatealpha(
            $this->overlayImage,
            $backgroundColor[0],
            $backgroundColor[1],
            $backgroundColor[2],
            $backgroundColor[3]
        );

        imagefill($this->overlayImage, 0, 0, $bgColor);
    }

    public function addTextToOverlay($text, $fontSize = 20, $textColor = [255, 255, 255], $opacity = 0)
    {
        if (!$this->overlayImage) {
            throw new Exception("Overlay image not created.");
        }

        $width = imagesx($this->overlayImage);
        $height = imagesy($this->overlayImage);

        $textColorAlpha = imagecolorallocatealpha(
            $this->overlayImage,
            $textColor[0],
            $textColor[1],
            $textColor[2],
            $opacity
        );

        $bbox = imagettfbbox($fontSize, 0, $this->fontPath, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2 + $textHeight;

        imagettftext($this->overlayImage, $fontSize, 0, $x, $y, $textColorAlpha, $this->fontPath, $text);
    }

    public function combineImages($outputPath)
    {
        if (!$this->baseImage || !$this->overlayImage) {
            throw new Exception("Base or overlay image not created.");
        }

        $baseWidth = imagesx($this->baseImage);
        $baseHeight = imagesy($this->baseImage);
        $overlayWidth = imagesx($this->overlayImage);
        $overlayHeight = imagesy($this->overlayImage);

        $destX = ($baseWidth - $overlayWidth) / 2;
        $destY = ($baseHeight - $overlayHeight) / 2;

        imagecopy($this->baseImage, $this->overlayImage, $destX, $destY, 0, 0, $overlayWidth, $overlayHeight);

        imagepng($this->baseImage, $outputPath);

        imagedestroy($this->baseImage);
        imagedestroy($this->overlayImage);
    }
}
