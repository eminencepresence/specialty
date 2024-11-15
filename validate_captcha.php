<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $userTextCaptcha = $_POST['text_captcha'];
    $turnstileResponse = $_POST['cf-turnstile-response'];
    $secretKey = getenv('TURNSTILE_SECRET_KEY'); 

    // Validate text-based CAPTCHA
    if ($userTextCaptcha !== $_SESSION['captcha_text']) {
        $_SESSION['error_message'] = "Text CAPTCHA verification failed. Please try again.";
        header("Location: index.php");
        exit;
    }

    // Validate Turnstile CAPTCHA
    $data = [
        'secret' => $secretKey,
        'response' => $turnstileResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    $verify = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);
    $captchaSuccess = json_decode($verify);

    if (!$captchaSuccess->success) {
        $_SESSION['error_message'] = "Cloudflare CAPTCHA verification failed. Please try again.";
        header("Location: index.php");
        exit;
    }

    $_SESSION['error_message'] = null;
    header("Location: success.php"); // Adjust this to your desired success page
    exit;
}
?>
