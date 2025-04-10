<?php
// File: /api/join_trip.php
session_start();

// Debug
error_log("join_trip.php accessed for trip_id: " . (isset($_GET['trip_id']) ? $_GET['trip_id'] : 'null') . ", type: " . (isset($_GET['type']) ? $_GET['type'] : 'null'));

// Include db.php
require_once __DIR__ . '/../config/db.php';

// Debug PDO
if (!isset($pdo)) {
    error_log("PDO is not set in join_trip.php");
    die("Database connection failed");
} else {
    error_log("PDO connection active. Server Info: " . $pdo->getAttribute(PDO::ATTR_SERVER_INFO));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
    $pdo->setAttribute(PDO::ATTR_TIMEOUT, 30);
    $pdo->beginTransaction();
}

// Ensure user is logged in
if (!isset($_SESSION['user']['id'])) {
    error_log("User not logged in");
    $pdo->rollBack();
    die("Not authorized");
}
$user_id = $_SESSION['user']['id'];
error_log("User ID: " . $user_id);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['trip_id']) && isset($_GET['type'])) {
    $trip_id = (int)$_GET['trip_id']; // Cast to integer for safety
    $trip_type = $_GET['type'];

    try {
        error_log("Processing join request for trip_id: $trip_id, type: $trip_type");

        // Validate trip type (solo only)
        if ($trip_type !== 'solo') {
            error_log("Invalid trip type: $trip_type (solo only supported)");
            $pdo->rollBack();
            header("Location: ../public/pages/dashboard.php?error=invalid_trip_type");
            exit;
        }

        // Check if the trip exists and user is not the creator
        $check_stmt = $pdo->prepare("
            SELECT 1 FROM solo_trips WHERE id = :trip_id AND created_by != :user_id
        ");
        $check_stmt->execute([':trip_id' => $trip_id, ':user_id' => $user_id]);
        if (!$check_stmt->fetch()) {
            error_log("Trip $trip_id not found or created by user $user_id. Solo_trips data: " . print_r($pdo->query("SELECT id, created_by FROM solo_trips WHERE id = $trip_id")->fetch(), true));
            $pdo->rollBack();
            header("Location: ../public/pages/dashboard.php?error=invalid_trip");
            exit;
        }
        error_log("Trip $trip_id validated");

        // Lock and check if user already requested or joined
        $check_membership_stmt = $pdo->prepare("
            SELECT * FROM trip_members WHERE trip_id = :trip_id AND user_id = :user_id AND status IN ('pending', 'approved') FOR UPDATE
        ");
        $check_membership_stmt->execute([':trip_id' => $trip_id, ':user_id' => $user_id]);
        if ($check_membership_stmt->fetch()) {
            error_log("User $user_id already requested or joined trip $trip_id");
            $pdo->rollBack();
            header("Location: ../public/pages/dashboard.php?error=already_requested");
            exit;
        }
        error_log("No prior request found for user $user_id on trip $trip_id");

        // Insert join request into trip_members
        $join_stmt = $pdo->prepare("
            INSERT INTO trip_members (trip_id, user_id, status, joined_at)
            VALUES (:trip_id, :user_id, 'pending', NOW())
        ");
        error_log("Executing INSERT with trip_id: $trip_id, user_id: $user_id");
        $join_stmt->execute([
            ':trip_id' => $trip_id,
            ':user_id' => $user_id
        ]);
        error_log("Insert executed successfully for trip_id: $trip_id, user_id: $user_id");

        // Commit transaction
        error_log("Attempting to commit transaction");
        $pdo->commit();
        error_log("Transaction committed successfully");

        header("Location: ../public/pages/dashboard.php?success=join_requested");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Database error in join_trip.php: " . $e->getMessage() . " (SQLSTATE: " . $e->getCode() . ", Error Info: " . print_r($e->errorInfo, true) . ", Backtrace: " . print_r(debug_backtrace(), true) . ")");
        header("Location: ../public/pages/dashboard.php?error=join_failed");
        exit;
    }
} else {
    error_log("Invalid request method or missing parameters");
    $pdo->rollBack();
    header("Location: ../public/pages/dashboard.php");
    exit;
}
?>