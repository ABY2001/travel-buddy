<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE name = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'is_admin' => $user['is_admin'],
        ];

        
        if ($user['is_admin'] == 1) {
            header("Location: ../admin/admin-dashboard.php");
        } else {
            header("Location: ../public/pages/dashboard.php");
        }
        exit();
    } else {
        // echo "Invalid username or password";
        header("Location: ../public/pages/login.php");
    }
}

?>
