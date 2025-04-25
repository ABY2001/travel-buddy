<?php
// File: /travel-buddy/api/delete_trip_user.php
session_start();
require_once __DIR__ . '/../config/db.php';
include '../api/notifications.php';

if (!isset($_SESSION['user']['id']) || !isset($_GET['trip_id'])) {
    setNotification("Unauthorized action.", "error");
    header("Location: /travel-buddy/public/pages/dashboard.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$trip_id = intval($_GET['trip_id']);

try {
    $stmt = $pdo->prepare("SELECT created_by, travel_date FROM solo_trips WHERE id = ?");
    $stmt->execute([$trip_id]);
    $trip = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trip) {
        setNotification("Invalid trip ID.", "error");
        header("Location: /travel-buddy/public/pages/dashboard.php");
        exit;
    }

    if ($trip['created_by'] !== $user_id) {
        setNotification("You are not authorized to delete this trip.", "error");
        header("Location: /travel-buddy/public/pages/dashboard.php");
        exit;
    }

    // Check if the trip has already started
    $travel_date = new DateTime($trip['travel_date']);
    $current_date = new DateTime();
    if ($travel_date <= $current_date) {
        setNotification("Cannot delete a trip that has already started.", "error");
        header("Location: /travel-buddy/public/pages/dashboard.php");
        exit;
    }

    // Delete related trip members first to avoid foreign key constraint issues
    $delete_members = $pdo->prepare("DELETE FROM trip_members WHERE trip_id = ?");
    $delete_members->execute([$trip_id]);

    // Delete the trip
    $delete_trip = $pdo->prepare("DELETE FROM solo_trips WHERE id = ?");
    $delete_trip->execute([$trip_id]);

    // Debug log
    error_log("Trip $trip_id deleted by user $user_id");
    setNotification("Trip deleted successfully!", "success");
    header("Location: /travel-buddy/public/pages/dashboard.php?section=my-trips");
    exit;
} catch (PDOException $e) {
    error_log("delete_trip_user error: " . $e->getMessage());
    setNotification("Failed to delete trip.", "error");
    header("Location: /travel-buddy/public/pages/dashboard.php?section=my-trips");
    exit;
}
?>