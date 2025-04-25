<?php
// session_start(); // Ensure sessions are started

// Function to set a notification message
function setNotification($message, $type = 'success') {
    $_SESSION['notification'] = [
        'message' => $message,
        'type' => $type // Can be 'success', 'error', 'warning', etc.
    ];
}

// Function to get and clear the notification message
function getNotification() {
    if (isset($_SESSION['notification'])) {
        $notification = $_SESSION['notification'];
        unset($_SESSION['notification']); // Clear after retrieving
        return $notification;
    }
    return null;
}
?>