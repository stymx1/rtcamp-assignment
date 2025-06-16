<?php
 // Generate verification code
function getAllTasks(): array {
    $file = __DIR__ . '/tasks.txt';
    if (!file_exists($file)) return [];

    $data = file_get_contents($file);
    return json_decode($data, true) ?? [];
}

function saveAllTasks(array $tasks): void {
    $file = __DIR__ . '/tasks.txt';
    file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
}

function addTask(string $task_name): bool {
    $tasks = getAllTasks();

    foreach ($tasks as $task) {
        if (strcasecmp($task['name'], $task_name) === 0) return false;
    }

    $tasks[] = [
        'id' => uniqid(),
        'name' => $task_name,
        'completed' => false
    ];

    saveAllTasks($tasks);
    return true;
}

function markTaskAsCompleted(string $task_id, bool $is_completed): bool {
    $tasks = getAllTasks();
    $found = false;

    foreach ($tasks as &$task) {
        if ($task['id'] === $task_id) {
            $task['completed'] = $is_completed;
            $found = true;
            break;
        }
    }

    saveAllTasks($tasks);
    return $found;
}

function deleteTask(string $task_id): bool {
    $tasks = getAllTasks();
    $newTasks = array_filter($tasks, fn($task) => $task['id'] !== $task_id);

    if (count($newTasks) === count($tasks)) return false;

    saveAllTasks(array_values($newTasks));
    return true;
}


function generateVerificationCode(): string {
    return str_pad(strval(random_int(0, 999999)), 6, '0', STR_PAD_LEFT);
}

function subscribeEmail( string $email ): bool {
    $file = __DIR__ . '/pending_subscriptions.txt';
    
    // Generate verification code
    $code = generateVerificationCode();

    // Save to pending subscriptions
    $entry = "$email|$code\n";
    file_put_contents($file, $entry, FILE_APPEND);

    // Generate verification link
    $verification_link = "http://localhost/task-scheduler-SnehaMahajan07/src/verify.php?email=" . urlencode($email) . "&code=" . urlencode($code);

    // Email body
    $body = "Hello,\n\nPlease verify your email address by clicking the link below:\n\n$verification_link\n\nThanks,\nTask Planner Team";

    // Create logs folder if not exists
    $logDir = __DIR__ . '/logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }

    // Save to file
    file_put_contents($logDir . '/verification_emails.txt', "TO: $email\n$body\n\n", FILE_APPEND);

    return true;
}

function verifySubscription(string $email, string $code): bool {
    $pending_file = __DIR__ . '/pending_subscriptions.txt';
    $subscribers_file = __DIR__ . '/subscribers.txt';

    $pending = [];
    if (file_exists($pending_file)) {
        $lines = file($pending_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            [$pendingEmail, $pendingCode] = explode('|', $line);
            $pending[trim($pendingEmail)] = trim($pendingCode);
        }
    }

    if (!isset($pending[$email]) || $pending[$email] !== $code) {
        return false;
    }

    // Remove verified entry
    $newPending = [];
    foreach ($pending as $e => $c) {
        if (!($e === $email && $c === $code)) {
            $newPending[] = "$e|$c";
        }
    }
    file_put_contents($pending_file, implode("\n", $newPending));

    // Save to subscribers list
    $subscribers = file_exists($subscribers_file) ? json_decode(file_get_contents($subscribers_file), true) : [];
    if (!in_array($email, $subscribers)) {
        $subscribers[] = $email;
        file_put_contents($subscribers_file, json_encode($subscribers, JSON_PRETTY_PRINT));
    }

    return true;
}


function unsubscribeEmail(string $email): bool {
    $subscribers_file = __DIR__ . '/subscribers.txt';
    $subscribers = file_exists($subscribers_file) ? json_decode(file_get_contents($subscribers_file), true) : [];

    $filtered = array_filter($subscribers, fn($e) => $e !== $email);

    if (count($filtered) === count($subscribers)) return false;

    file_put_contents($subscribers_file, json_encode(array_values($filtered), JSON_PRETTY_PRINT));
    return true;
}

function sendTaskReminders(): void {
    $subscribers_file = __DIR__ . '/subscribers.txt';
    $subscribers = file_exists($subscribers_file) ? json_decode(file_get_contents($subscribers_file), true) : [];

    $pending_tasks = array_filter(getAllTasks(), fn($t) => !$t['completed']);

    foreach ($subscribers as $email) {
        sendTaskEmail($email, $pending_tasks);
    }
}

function sendTaskEmail(string $email, array $pending_tasks): bool {
    $subject = 'Task Planner - Pending Tasks Reminder';
    $body = "Hello,\n\nYou have the following pending tasks:\n\n";

    foreach ($pending_tasks as $task) {
        $body .= "- " . $task['name'] . "\n";
    }

    $body .= "\nPlease complete them soon.\n\nRegards,\nTask Planner App";

    // Email headers
    $headers = "From: no-reply@taskplanner.local\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Send the email
    return mail($email, $subject, $body, $headers);
}
