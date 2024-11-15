<?php
session_start();

// Generate random text for CAPTCHA
$captchaText = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 6);
$_SESSION['captcha_text'] = $captchaText;

// Create a larger image
$imageWidth = 200;
$imageHeight = 100;
$image = imagecreatetruecolor($imageWidth, $imageHeight);

// Set colors
$bgColor = imagecolorallocate($image, 255, 255, 255);
$textColor = imagecolorallocate($image, 0, 0, 0);
$lineColor = imagecolorallocate($image, 64, 64, 64);

// Fill the background
imagefilledrectangle($image, 0, 0, $imageWidth, $imageHeight, $bgColor);

// Add random lines for extra security
for ($i = 0; $i < 5; $i++) {
    imageline($image, 0, rand(0, $imageHeight), $imageWidth, rand(0, $imageHeight), $lineColor);
}

// Set the path to the font file
$fontFile = __DIR__ . '/assets/fonts/Roboto-Regular.ttf';

// Debug: Check if font file exists
if (!file_exists($fontFile)) {
    die("Font file not found: " . $fontFile);
}

// Set font size for TrueType font
$fontSize = 24;

// Calculate the bounding box to center the text
$bbox = imagettfbbox($fontSize, 0, $fontFile, $captchaText);
if (!$bbox) {
    die("Could not read font: " . $fontFile);
}

$textWidth = $bbox[2] - $bbox[0];
$textHeight = $bbox[1] - $bbox[7];
$textX = ($imageWidth - $textWidth) / 2;
$textY = ($imageHeight - $textHeight) / 2 + $textHeight;

// Add the text to the image
if (!imagettftext($image, $fontSize, 0, $textX, $textY, $textColor, $fontFile, $captchaText)) {
    die("Could not render text with font: " . $fontFile);
}

// Output the image as a PNG
header("Content-type: image/png");
imagepng($image);

// Clean up
imagedestroy($image);
?>
