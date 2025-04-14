<?php
// File: /api/delete_trip.php
session_start();
require_once __DIR__ . '/../config/db.php';

// Check if user is logged in and trip_id is provided via POST
if (!isset($_SESSION['user']['id']) || !isset($_POST['trip_id'])) {
    error_log("Unauthorized action or missing trip_id in delete_trip.php. Session user_id: " . (isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 'not set') . " at " . date('Y-m-d H:i:s'));
    header("Location: /travel-buddy/admin/admin-dashboard.php?msg=Unauthorized+action");
    exit;
}

$user_id = $_SESSION['user']['id'];
$trip_id = $_POST['trip_id'];

try {
    // Fetch trip details to validate travel date (no creator check for admin)
    $stmt = $pdo->prepare("SELECT travel_date FROM solo_trips WHERE id = :trip_id");
    $stmt->execute([':trip_id' => $trip_id]);
    $trip = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($trip) {
        $current_date = new DateTime(); // Current date and time
        $trip_date = new DateTime($trip['travel_date']);

        if ($trip_date <= $current_date) {
            error_log("Trip deletion blocked: travel_date ($trip_date) is in the past for trip_id: $trip_id at " . date('Y-m-d H:i:s'));
            header("Location: /travel-buddy/admin/admin-dashboard.php?msg=Trip+has+already+started");
            exit;
        } else {
            // Delete associated trip members first to avoid foreign key constraints
            $delete_members = $pdo->prepare("DELETE FROM trip_members WHERE trip_id = :trip_id");
            $delete_members->execute([':trip_id' => $trip_id]);

            // Delete the trip (no creator check for admin)
            $delete_trip = $pdo->prepare("DELETE FROM solo_trips WHERE id = :trip_id");
            $delete_trip->execute([':trip_id' => $trip_id]);

            if ($delete_trip->rowCount() > 0) {
                error_log("Trip deleted successfully: trip_id $trip_id by user_id $user_id at " . date('Y-m-d H:i:s'));
                header("Location: /travel-buddy/admin/admin-dashboard.php?msg=Trip+deleted+successfully");
            } else {
                error_log("No rows deleted for trip_id: $trip_id by user_id: $user_id, possibly not found at " . date('Y-m-d H:i:s'));
                header("Location: /travel-buddy/admin/admin-dashboard.php?msg=Failed+to+delete+trip");
            }
        }
    } else {
        error_log("Trip not found for trip_id: $trip_id at " . date('Y-m-d H:i:s'));
        header("Location: /travel-buddy/admin/admin-dashboard.php?msg=Invalid+trip");
    }
} catch (PDOException $e) {
    error_log("Database error deleting trip $trip_id: " . $e->getMessage() . " (SQLSTATE: " . $e->getCode() . ") at " . date('Y-m-d H:i:s'));
    if (isset($stmt) && $stmt instanceof PDOStatement) {
        error_log("SQL Error Info: " . print_r($stmt->errorInfo(), true));
    }
    header("Location: /travel-buddy/admin/admin-dashboard.php?msg=Failed+to+delete+trip");
}
?>