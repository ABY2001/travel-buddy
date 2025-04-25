<?php
session_start();
include '../config/db.php'; // From public/api/ to root config/
include '../api/notifications.php'; // Include notifications helper

// Ensure user is logged in
if (!isset($_SESSION['user']['id'])) {
    setNotification("Not authorized.", "error");
    header("Location: /travel-buddy/public/pages/dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trip_type = $_POST['trip_type'] ?? '';
    $destination = trim($_POST['destination'] ?? '');
    $travel_date = trim($_POST['travel_date'] ?? '');
    $ending_date = trim($_POST['ending_date'] ?? ''); // New field
    $budget = floatval($_POST['budget'] ?? 0);
    $gender_preference = $_POST['gender_preference'] ?? 'any';
    $created_by = $_SESSION['user']['id']; // Get logged-in user ID
    $total_members = $_POST['total_members'] ?? ''; // For group trips

    // Validate inputs
    $valid_gender_preferences = ['male', 'female', 'other', 'any'];
    if (!in_array($gender_preference, $valid_gender_preferences)) {
        setNotification("Invalid gender preference.", "error");
        header("Location: /travel-buddy/public/pages/create_trip.php");
        exit;
    }
    if (empty($destination) || empty($travel_date) || empty($ending_date) || $budget <= 0) {
        setNotification("Missing or invalid required fields.", "error");
        header("Location: /travel-buddy/public/pages/create_trip.php");
        exit;
    }
    // Validate that ending_date is after travel_date
    $travelDate = new DateTime($travel_date);
    $endingDate = new DateTime($ending_date);
    if ($endingDate <= $travelDate) {
        setNotification("Ending date must be after travel date.", "error");
        header("Location: /travel-buddy/public/pages/create_trip.php");
        exit;
    }

    // Debugging
    error_log("Received POST data: " . print_r($_POST, true));

    // Start transaction
    $pdo->beginTransaction();

    try {
        if ($trip_type === 'solo') {
            $stmt = $pdo->prepare("
                INSERT INTO solo_trips (destination, travel_date, ending_date, budget, gender_preference, created_by, created_at, status)
                VALUES (:destination, :travel_date, :ending_date, :budget, :gender_preference, :created_by, NOW(), 'active')
            ");
            $stmt->execute([
                ':destination' => $destination,
                ':travel_date' => $travel_date,
                ':ending_date' => $ending_date,
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
        setNotification("Trip created successfully!", "success");
        header("Location: /travel-buddy/public/pages/dashboard.php?section=my-trips");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Database error in trips.php: " . $e->getMessage());
        setNotification("Failed to create trip: " . $e->getMessage(), "error");
        header("Location: /travel-buddy/public/pages/create_trip.php");
        exit;
    }
} else {
    // If not POST, redirect back
    header("Location: /travel-buddy/public/pages/create_trip.php");
    exit;
}
?>