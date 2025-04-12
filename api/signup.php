<?php
session_start();
require_once __DIR__ . "/../config/db.php";

ob_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;
    $age = htmlspecialchars($_POST['age'] ?? '');
    $phone_number = htmlspecialchars($_POST['phone_number'] ?? '');
    $gender = htmlspecialchars($_POST['gender'] ?? '');
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Validate gender
    $valid_genders = ['male', 'female', 'other'];
    if (!in_array($gender, $valid_genders)) {
        die("Invalid gender selection");
    }

    if (!isset($pdo)) {
        die("Database connection not found.");
    }

    try {
        $query = "INSERT INTO users (name, email, password, age, gender, phone_number, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$name, $email, $password, $age, $gender, $phone_number, $is_admin]);

        // Get the last inserted ID and set it in the session
        $user_id = $pdo->lastInsertId();
        $_SESSION['user'] = [
            'id' => $user_id,
            'name' => $name,
            'email' => $email,
            'is_admin' => $is_admin
        ];

        if ($is_admin == 1) {
            header("Location: ../admin/dashboard.php"); // Absolute path for admin
        } else {
            header("Location: ../public/pages/dashboard.php"); // Absolute path for normal user
        }
        exit();
    } catch (PDOException $e) {
        error_log("Signup error: " . $e->getMessage());
        header("Location: /public/pages/signup.php?error=signup_failed");
        exit();
    }
} else {
    header("Location: /public/pages/register.php");
    exit();
}

ob_end_flush();
?>