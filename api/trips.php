<?php
session_start();
include '../config/db.php'; // From public/api/ to root config/

// Ensure user is logged in
if (!isset($_SESSION['user']['id'])) {
    die("Not authorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trip_type = $_POST['trip_type'] ?? '';
    $destination = trim($_POST['destination'] ?? '');
    $travel_date = trim($_POST['travel_date'] ?? '');
    $budget = floatval($_POST['budget'] ?? 0);
    $gender_preference = $_POST['gender_preference'] ?? 'any';
    $created_by = $_SESSION['user']['id']; // Get logged-in user ID
    $total_members = $_POST['total_members'] ?? ''; // For group trips

    // Validate inputs
    $valid_gender_preferences = ['male', 'female', 'other', 'any'];
    if (!in_array($gender_preference, $valid_gender_preferences)) {
        throw new Exception("Invalid gender preference");
    }
    if (empty($destination) || empty($travel_date) || $budget <= 0) {
        throw new Exception("Missing or invalid required fields");
    }

    // Debugging
    error_log("Received POST data: " . print_r($_POST, true));

    // Start transaction
    $pdo->beginTransaction();

    try {
        if ($trip_type === 'solo') {
            $stmt = $pdo->prepare("
                INSERT INTO solo_trips (destination, travel_date, budget, gender_preference, created_by)
                VALUES (:destination, :travel_date, :budget, :gender_preference, :created_by)
            ");
            $stmt->execute([
                ':destination' => $destination,
                ':travel_date' => $travel_date,
                ':budget' => $budget,
                ':gender_preference' => $gender_preference,
                ':created_by' => $created_by
            ]);
        } elseif ($trip_type === 'group') {
            if (empty($total_members) || !is_numeric($total_members) || $total_members <= 0) {
                throw new Exception("Invalid total members");
            }
            $stmt = $pdo->prepare("
                INSERT INTO group_trips (destination, travel_date, gender_preference, total_members, created_by, budget)
                VALUES (:destination, :travel_date, :gender_preference, :total_members, :created_by, :budget)
            ");
            $stmt->execute([
                ':destination' => $destination,
                ':travel_date' => $travel_date,
                ':gender_preference' => $gender_preference,
                ':total_members' => $total_members,
                ':created_by' => $created_by,
                ':budget' => $budget
            ]);
        } else {
            throw new Exception("Invalid trip type");
        }

        $pdo->commit();
        error_log("Trip created successfully for user $created_by (type: $trip_type)");
        header("Location: ../public/pages/dashboard.php?success=trip_created");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Database error in trips.php: " . $e->getMessage());
        header("Location: ../public/pages/create_trip.php?error=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    // If not POST, redirect back
    header("Location: ../public/pages/create_trip.php");
    exit;
}
?>