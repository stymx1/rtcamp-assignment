<?php
require_once 'functions.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $code = trim($_POST['code'] ?? '');

    if ($email && $code) {
        if (verifySubscription($email, $code)) {
            $message = "✅ Email verified successfully!";
        } else {
            $message = "❌ Invalid verification code or email.";
        }
    } else {
        $message = "⚠️ Please enter both email and verification code.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Email - Task Planner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 50px;
            background-color: #f2f2f2;
        }
        .verify-container {
            background: white;
            padding: 30px;
            max-width: 400px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type="email"], input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            padding: 10px 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="verify-container">
    <h2>Verify Your Email</h2>
    <form method="POST" action="">
        <label for="email">Your Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="code">Verification Code:</label>
        <input type="text" name="code" id="code" required>

        <button type="submit">Verify</button>
    </form>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
</div>

</body>
</html>
