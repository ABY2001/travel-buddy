<?php
// File: /api/delete_user.php
session_start();
require_once __DIR__ . '/../config/db.php';

// Check if user is logged in and user_id is provided via POST
if (!isset($_SESSION['user']['id']) || !isset($_POST['user_id'])) {
    error_log("Unauthorized action or missing user_id in delete_user.php. Session user_id: " . (isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 'not set') . " at " . date('Y-m-d H:i:s'));
    header("Location: /travel-buddy/admin/admin-dashboard.php?msg=Unauthorized+action");
    exit;
}

$user_id = $_SESSION['user']['id']; // Admin user ID
$target_user_id = $_POST['user_id']; // User to delete

// Prevent admin from deleting themselves (optional safety check)
if ($user_id == $target_user_id) {
    error_log("Admin attempted to delete own account (user_id: $user_id) at " . date('Y-m-d H:i:s'));
    header("Location: /travel-buddy/admin/admin-dashboard.php?msg=Cannot+delete+your+own+account");
    exit;
}

try {
    // Start transaction to ensure consistency
    $pdo->beginTransaction();

    // Delete user's trips first to avoid foreign key constraints
    $delete_trips = $pdo->prepare("DELETE FROM solo_trips WHERE created_by = :user_id");
    $delete_trips->execute([':user_id' => $target_user_id]);

    // Delete user's trip memberships
    $delete_members = $pdo->prepare("DELETE FROM trip_members WHERE user_id = :user_id");
    $delete_members->execute([':user_id' => $target_user_id]);

    // Delete the user
    $delete_user = $pdo->prepare("DELETE FROM users WHERE id = :user_id");
    $delete_user->execute([':user_id' => $target_user_id]);

    if ($delete_user->rowCount() > 0) {
        error_log("User deleted successfully: user_id $target_user_id by admin $user_id at " . date('Y-m-d H:i:s'));
        $pdo->commit();
        header("Location: /travel-buddy/admin/admin-dashboard.php?msg=User+deleted+successfully");
    } else {
        error_log("No user found to delete for user_id: $target_user_id by admin $user_id at " . date('Y-m-d H:i:s'));
        $pdo->rollBack();
        header("Location: /travel-buddy/admin/admin-dashboard.php?msg=Failed+to+delete+user");
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Database error deleting user $target_user_id: " . $e->getMessage() . " (SQLSTATE: " . $e->getCode() . ") at " . date('Y-m-d H:i:s'));
    if (isset($delete_user) && $delete_user instanceof PDOStatement) {
        error_log("SQL Error Info: " . print_r($delete_user->errorInfo(), true));
    }
    header("Location: /travel-buddy/admin/admin-dashboard.php?msg=Failed+to+delete+user");
}
?>