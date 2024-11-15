<?php
session_start();

$captchaText = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 6);
$_SESSION['captcha_text'] = $captchaText;

$imageWidth = 200;
$imageHeight = 100;
$image = imagecreatetruecolor($imageWidth, $imageHeight);

$bgColor = imagecolorallocate($image, 255, 255, 255);
$textColor = imagecolorallocate($image, 0, 0, 0);
$lineColor = imagecolorallocate($image, 64, 64, 64);

imagefilledrectangle($image, 0, 0, $imageWidth, $imageHeight, $bgColor);

for ($i = 0; $i < 5; $i++) {
    imageline($image, 0, rand(0, $imageHeight), $imageWidth, rand(0, $imageHeight), $lineColor);
}

$fontFile = __DIR__ . '/assets/fonts/Roboto-Regular.ttf';
$fontSize = 24;
$bbox = imagettfbbox($fontSize, 0, $fontFile, $captchaText);
$textWidth = $bbox[2] - $bbox[0];
$textHeight = $bbox[1] - $bbox[7];
$textX = ($imageWidth - $textWidth) / 2;
$textY = ($imageHeight - $textHeight) / 2 + $textHeight;

imagettftext($image, $fontSize, 0, $textX, $textY, $textColor, $fontFile, $captchaText);

header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>
