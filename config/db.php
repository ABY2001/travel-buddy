
<?php
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        die("Error: .env file not found.");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Skip comments
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

// Load the .env file
loadEnv(__DIR__ . '/../.env'); // Moves two levels up


$dsn = "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME');
$dbusername = getenv('DB_USER');
$dbpassword = getenv('DB_PASS');

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Connection failed");
}
