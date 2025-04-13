<?php
// File: /api/delete_trip.php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user']['id']) || !isset($_GET['trip_id'])) {
    header("Location: /travel-buddy/public/pages/dashboard.php?error=unauthorized_action");
    exit;
}

$user_id = $_SESSION['user']['id'];
$trip_id = $_GET['trip_id'];

try {
    $stmt = $pdo->prepare("SELECT created_by, travel_date FROM solo_trips WHERE id = :trip_id");
    $stmt->execute([':trip_id' => $trip_id]);
    $trip = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($trip) {
        $current_date = new DateTime(); // Use current system date and time
        $trip_date = new DateTime($trip['travel_date']);

        if ($trip['created_by'] != $user_id) {
            header("Location: /travel-buddy/public/pages/dashboard.php?error=unauthorized_action");
        } elseif ($trip_date <= $current_date) {
            header("Location: /travel-buddy/public/pages/dashboard.php?error=trip_started");
        } else {
            // Delete associated trip members first to avoid foreign key constraints
            $delete_members = $pdo->prepare("DELETE FROM trip_members WHERE trip_id = :trip_id");
            $delete_members->execute([':trip_id' => $trip_id]);

            // Delete the trip
            $delete_trip = $pdo->prepare("DELETE FROM solo_trips WHERE id = :trip_id AND created_by = :user_id");
            $delete_trip->execute([':trip_id' => $trip_id, ':user_id' => $user_id]);

            if ($delete_trip->rowCount() > 0) {
                header("Location: /travel-buddy/public/pages/dashboard.php?success=deleted");
            } else {
                header("Location: /travel-buddy/public/pages/dashboard.php?error=delete_failed");
            }
        }
    } else {
        header("Location: /travel-buddy/public/pages/dashboard.php?error=invalid_trip");
    }
} catch (PDOException $e) {
    error_log("Error deleting trip: " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));
    header("Location: /travel-buddy/public/pages/dashboard.php?error=delete_failed");
}
?>