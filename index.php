<?php
require_once 'functions.php';

// Handle Task Add
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['task-name'])) {
    $taskName = trim($_POST['task-name']);
    if ($taskName !== '') {
        addTask($taskName);
    }
}

// Handle Task Completion/Deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && isset($_POST['task-id'])) {
    $taskId = $_POST['task-id'];
    if ($_POST['action'] === 'toggle') {
        markTaskAsCompleted($taskId, $_POST['status'] === '1');
    } elseif ($_POST['action'] === 'delete') {
        deleteTask($taskId);
    }
}

// Handle Subscription
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    if ($email !== '') {
        subscribeEmail($email);
    }
}

$tasks = getAllTasks();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Task Planner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="email"] {
            width: 75%;
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button.delete-task {
            background-color: #dc3545;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li.task-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .unsubscribe-link {
            text-align: center;
            margin-top: 25px;
        }

        .unsubscribe-link a {
            color: #dc3545;
            text-decoration: none;
            font-weight: bold;
        }

        .unsubscribe-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>📋 Task Planner</h1>

        <!-- Add Task Form -->
        <form method="POST" action="">
            <input type="text" name="task-name" placeholder="Enter new task" required>
            <button type="submit">Add Task</button>
        </form>

        <!-- Link to Verify Email -->
<div style="margin-top: 20px;">
    <p>Already subscribed but not verified?</p>
    <a href="verify.php" style="color: #007BFF; text-decoration: none;">🔐 Click here to verify your email</a>
</div>


        <!-- Tasks List -->
        <ul>
            <?php foreach ($tasks as $task): ?>
                <li class="task-item">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="task-id" value="<?= $task['id'] ?>">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="status" value="<?= $task['completed'] ? '0' : '1' ?>">
                        <input type="checkbox" onchange="this.form.submit()" <?= $task['completed'] ? 'checked' : '' ?>>
                    </form>
                    <span><?= htmlspecialchars($task['name']) ?></span>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="task-id" value="<?= $task['id'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="delete-task">Delete</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Subscription Form -->
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter your email to subscribe" required>
            <button type="submit">Subscribe</button>
        </form>

        <!-- Unsubscribe Link -->
        <div class="unsubscribe-link">
            <a href="unsubscribe.php">Unsubscribe from Emails</a>
        </div>
    </div>
</body>

</html>
