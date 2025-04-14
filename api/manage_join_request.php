<?php
// File: /travel-buddy/api/manage_join_request.php
session_start();

// Debug
error_log("manage_join_request.php accessed for request_id: " . (isset($_GET['request_id']) ? $_GET['request_id'] : 'null') . ", action: " . (isset($_GET['action']) ? $_GET['action'] : 'null'));

// Include db.php
require_once __DIR__ . '/../config/db.php';

// Debug PDO
if (!isset($pdo)) {
    error_log("PDO is not set in manage_join_request.php");
    die("Database connection failed");
} else {
    error_log("PDO connection active. Server Info: " . $pdo->getAttribute(PDO::ATTR_SERVER_INFO));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['request_id']) && isset($_GET['action'])) {
    $request_id = $_GET['request_id'];
    $action = $_GET['action'];

    try {
        error_log("Processing request_id: $request_id, action: $action");

        // Verify the request belongs to the user
        $check_stmt = $pdo->prepare("
            SELECT tm.trip_id, st.created_by
            FROM trip_members tm
            JOIN solo_trips st ON tm.trip_id = st.id
            WHERE tm.id = :request_id AND st.created_by = :user_id
        ");
        $check_stmt->execute([':request_id' => $request_id, ':user_id' => $user_id]);
        $request = $check_stmt->fetch();
        if (!$request) {
            error_log("Request $request_id not found or not owned by user $user_id");
            $pdo->rollBack();
            header("Location: ../public/pages/dashboard.php?section=pending-requests&error=unauthorized_action");
            exit;
        }
        $trip_id = $request['trip_id'];
        error_log("Request $request_id validated for user $user_id, trip_id: $trip_id");

        // Update request status
        if ($action === 'approve') {
            $update_stmt = $pdo->prepare("
                UPDATE trip_members SET status = 'approved' WHERE id = :request_id
            ");
            $update_stmt->execute([':request_id' => $request_id]);
            error_log("Request $request_id approved successfully");

            // Check for solo trip limit (only one approved member besides creator)
            $member_count_stmt = $pdo->prepare("
                SELECT COUNT(*) as count
                FROM trip_members tm
                JOIN solo_trips st ON tm.trip_id = st.id
                WHERE tm.trip_id = :trip_id AND tm.status = 'approved' AND tm.user_id != st.created_by
            ");
            $member_count_stmt->execute([':trip_id' => $trip_id]);
            $member_count = $member_count_stmt->fetchColumn();
            if ($member_count > 1) {
                $update_stmt->execute([':request_id' => $request_id, ':status' => 'pending']); // Revert to pending
                error_log("Solo trip limit exceeded for trip_id: $trip_id, reverting approval");
                $pdo->rollBack();
                header("Location: ../public/pages/dashboard.php?section=pending-requests&error=solo_trip_limit_exceeded");
                exit;
            }

            // Set buddy_id to the approved user's id
            $approved_user_stmt = $pdo->prepare("
                SELECT user_id FROM trip_members WHERE id = :request_id
            ");
            $approved_user_stmt->execute([':request_id' => $request_id]);
            $approved_user_id = $approved_user_stmt->fetchColumn();
            $update_trip_stmt = $pdo->prepare("
                UPDATE solo_trips SET buddy_id = :buddy_id WHERE id = :trip_id
            ");
            $update_trip_stmt->execute([':buddy_id' => $approved_user_id, ':trip_id' => $trip_id]);
            error_log("Set buddy_id to $approved_user_id for trip_id: $trip_id");

            // Reject all other pending requests for the same trip
            $reject_stmt = $pdo->prepare("
                UPDATE trip_members 
                SET status = 'rejected' 
                WHERE trip_id = :trip_id AND id != :request_id AND status = 'pending'
            ");
            $reject_stmt->execute([':trip_id' => $trip_id, ':request_id' => $request_id]);
            $rejected_count = $reject_stmt->rowCount();
            error_log("Rejected $rejected_count other pending requests for trip_id: $trip_id");
        } elseif ($action === 'reject') {
            $update_stmt = $pdo->prepare("
                UPDATE trip_members SET status = 'rejected' WHERE id = :request_id
            ");
            $update_stmt->execute([':request_id' => $request_id]);
            error_log("Request $request_id rejected successfully");
        } else {
            error_log("Invalid action: $action");
            $pdo->rollBack();
            header("Location: ../public/pages/dashboard.php?section=pending-requests&error=invalid_action");
            exit;
        }

        $pdo->commit();
        header("Location: ../public/pages/dashboard.php?section=pending-requests&success=join");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Database error in manage_join_request.php: " . $e->getMessage() . " (SQLSTATE: " . $e->getCode() . ") at " . date('Y-m-d H:i:s'));
        header("Location: ../public/pages/dashboard.php?section=pending-requests&error=update_failed");
        exit;
    }
} else {
    error_log("Invalid request method or missing parameters");
    $pdo->rollBack();
    header("Location: ../public/pages/dashboard.php?section=pending-requests");
    exit;
}
?>