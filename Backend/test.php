<?php
require_once 'C:\Users\joaop\Documents\GITHUB\HangmanGame\Backend\tools\createImage.php';

try {
    $fontPath = 'C:\Users\joaop\Documents\GITHUB\HangmanGame\Backend\assets\fonts\HachiMaruPop-Regular.ttf'; 

    $overlay = new ImageOverlay($fontPath);

    $baseImagePath = 'C:\Users\joaop\Documents\GITHUB\HangmanGame\Backend\assets\emailBackgroundImage.png'; 
    $overlay->loadBaseImage($baseImagePath);

    $overlayWidth = 400; 
    $overlayHeight = 200; 
    $overlay->createOverlayImage($overlayWidth, $overlayHeight);

    $text = 'iai matheus';
    $fontSize = 30;   
    $textColor = [0, 0, 0]; 
    $opacity = 50;   
    $overlay->addTextToOverlay($text, $fontSize, $textColor, $opacity);

    $outputPath = 'C:\Users\joaop\Documents\GITHUB\HangmanGame\Backend\saves\output.png'; 
    $overlay->combineImages($outputPath);

    echo "Imagem criada com sucesso: $outputPath\n";
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage() . "\n";
}