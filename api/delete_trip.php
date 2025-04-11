<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trip_id'])) {
    $tripId = $_POST['trip_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM solo_trips WHERE id = :id");
        $stmt->execute(['id' => $tripId]);

        header("Location: ../admin/admin-dashboard.php?msg=Trip+deleted+successfully");
        exit;
    } catch (PDOException $e) {
        // You can customize error messages here
        header("Location: ../admin/admin-dashboard.php?msg=Failed+to+delete+trip");
        exit;
    }
} else {
    header("Location: ../admin/admin-dashboard.php?msg=Invalid+request");
    exit;
}
