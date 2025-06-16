<?php
require_once 'functions.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    unsubscribeEmail($email);
    $message = "✅ Unsubscribed Successfully";
    $details = "You have been removed from task reminder notifications.";
} else {
    $message = "❌ Invalid Request";
    $details = "Missing email parameter.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe - Task Scheduler</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .btn { display: inline-block; padding: 10px 20px; background: #0078d4; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $message; ?></h1>
        <p><?php echo $details; ?></p>
        <a href="index.php" class="btn">🏠 Back to Task Scheduler</a>
    </div>
</body>
</html>