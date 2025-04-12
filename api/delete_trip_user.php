<?php
// File: /travel-buddy/api/delete_trip_user.php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user']['id']) || !isset($_GET['trip_id'])) {
    header("Location: /travel-buddy/public/pages/dashboard.php?error=unauthorized_action");
    exit;
}

$user_id = $_SESSION['user']['id'];
$trip_id = intval($_GET['trip_id']);

try {
    $stmt = $pdo->prepare("SELECT created_by FROM solo_trips WHERE id = ?");
    $stmt->execute([$trip_id]);
    $trip = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trip) {
        header("Location: /travel-buddy/public/pages/dashboard.php?error=invalid_trip");
        exit;
    }

    if ($trip['created_by'] !== $user_id) {
        header("Location: /travel-buddy/public/pages/dashboard.php?error=unauthorized_action");
        exit;
    }

    // Delete related trip members first to avoid foreign key constraint issues
    $delete_members = $pdo->prepare("DELETE FROM trip_members WHERE trip_id = ?");
    $delete_members->execute([$trip_id]);

    // Delete the trip
    $delete_trip = $pdo->prepare("DELETE FROM solo_trips WHERE id = ?");
    $delete_trip->execute([$trip_id]);

    // Debug log
    error_log("Redirecting to: /travel-buddy/public/pages/dashboard.php?success=deleted");
    // Use absolute path matching your server setup
    header("Location: http://localhost:3000/travel-buddy/public/pages/dashboard.php?success=deleted");
    exit;
} catch (PDOException $e) {
    error_log("delete_trip_user error: " . $e->getMessage());
    header("Location: /travel-buddy/public/pages/dashboard.php?error=delete_failed");
    exit;
}