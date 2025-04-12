<?php
// File: /api/dashboard.php
require_once __DIR__ . '/../config/db.php'; // Ensure this is included first

if (!isset($pdo) || $pdo === null) {
    error_log("PDO is not initialized in dashboard.php");
    die(json_encode(['error' => 'Database connection failed']));
}

function fetchUserTrips($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT st.*, 'solo' as trip_type, u.name AS creator_name, u.email AS creator_email
            FROM solo_trips st
            LEFT JOIN users u ON st.created_by = u.id
            WHERE st.created_by = :user_id
        ");
        $stmt->execute([':user_id' => $user_id]);
        $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add members for each trip
        foreach ($trips as &$trip) {
            $trip['members'] = fetchTripMembers($trip['id']);
        }
        return $trips;
    } catch (PDOException $e) {
        error_log("Database error in fetchUserTrips: " . $e->getMessage());
        return ['error' => 'Failed to fetch user trips'];
    }
}

function fetchJoinableTrips($user_id) {
    global $pdo;
    try {
        $user_stmt = $pdo->prepare("SELECT gender FROM users WHERE id = :user_id");
        $user_stmt->execute([':user_id' => $user_id]);
        $user = $user_stmt->fetch();
        $user_gender = $user['gender'];

        $stmt = $pdo->prepare("
            SELECT st.*, 'solo' as trip_type
            FROM solo_trips st
            WHERE st.created_by != :user_id
            AND st.id NOT IN (
                SELECT trip_id FROM trip_members WHERE user_id = :user_id AND status IN ('pending', 'approved')
            )
            AND (st.gender_preference = 'any' OR st.gender_preference = :user_gender)
        ");
        $stmt->execute([':user_id' => $user_id, ':user_gender' => $user_gender]);
        $joinable_trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("fetchJoinableTrips for user $user_id returned: " . print_r($joinable_trips, true));
        return $joinable_trips;
    } catch (PDOException $e) {
        error_log("Database error in fetchJoinableTrips: " . $e->getMessage());
        return ['error' => 'Failed to fetch joinable trips'];
    }
}

function fetchPendingJoinRequests($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT tm.id AS request_id, tm.trip_id, st.destination, st.travel_date, u.name, tm.status, tm.joined_at
            FROM trip_members tm
            JOIN solo_trips st ON tm.trip_id = st.id
            JOIN users u ON tm.user_id = u.id
            WHERE st.created_by = :user_id AND tm.status = 'pending'
        ");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in fetchPendingJoinRequests: " . $e->getMessage());
        return ['error' => 'Failed to fetch pending requests'];
    }
}

function fetchJoinedTrips($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT st.*, tm.status, 'solo' as trip_type, u.name AS creator_name, u.email AS creator_email
            FROM solo_trips st
            JOIN trip_members tm ON st.id = tm.trip_id
            LEFT JOIN users u ON st.created_by = u.id
            WHERE tm.user_id = :user_id AND tm.status IN ('pending', 'approved')
        ");
        $stmt->execute([':user_id' => $user_id]);
        $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add members for each trip
        foreach ($trips as &$trip) {
            $trip['members'] = fetchTripMembers($trip['id']);
        }
        return $trips;
    } catch (PDOException $e) {
        error_log("Database error in fetchJoinedTrips: " . $e->getMessage());
        return ['error' => 'Failed to fetch joined trips'];
    }
}

function fetchTripMembers($trip_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT u.id, u.name, u.email
            FROM trip_members tm
            JOIN users u ON tm.user_id = u.id
            WHERE tm.trip_id = :trip_id AND tm.status IN ('pending', 'approved') -- Include pending members
            UNION
            SELECT id, name, email FROM users WHERE id = (SELECT created_by FROM solo_trips WHERE id = :trip_id)
        ");
        $stmt->execute([':trip_id' => $trip_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in fetchTripMembers: " . $e->getMessage());
        return [];
    }
}
?>