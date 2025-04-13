<?php
// File: /api/dashboard.php
require_once __DIR__ . '/../config/db.php'; // Ensure this is included first

if (!isset($pdo) || $pdo === null) {
    error_log("PDO is not initialized in dashboard.php at " . date('Y-m-d H:i:s'));
    die(json_encode(['error' => 'Database connection failed']));
}

function fetchUserTrips($user_id) {
    global $pdo;
    try {
        $current_date = date('Y-m-d');
        $stmt = $pdo->prepare("
            SELECT st.*, u.name AS creator_name, u.email AS creator_email,
                   IF(st.ending_date < :current_date AND st.ending_date IS NOT NULL, 'completed', COALESCE(st.status, 'active')) AS status
            FROM solo_trips st
            LEFT JOIN users u ON st.created_by = u.id
            WHERE st.created_by = :user_id
        ");
        $stmt->execute([':user_id' => $user_id, ':current_date' => $current_date]);
        $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($trips)) {
            error_log("No trips found for user_id: $user_id. Checking all created_by: " . print_r($pdo->query("SELECT DISTINCT created_by FROM solo_trips")->fetchAll(PDO::FETCH_ASSOC), true));
            return []; // Return empty array instead of null
        } else {
            error_log("Found " . count($trips) . " trips for user_id: $user_id - Data: " . print_r($trips, true));
        }

        foreach ($trips as &$trip) {
            $trip['members'] = fetchTripMembers($trip['id']);
            if ($trip['ending_date'] && $trip['ending_date'] < $current_date && $trip['status'] !== 'completed') {
                $update_stmt = $pdo->prepare("UPDATE solo_trips SET status = 'completed' WHERE id = :id");
                $update_stmt->execute([':id' => $trip['id']]);
                $trip['status'] = 'completed';
            }
        }
        return $trips;
    } catch (PDOException $e) {
        error_log("Database error in fetchUserTrips: " . $e->getMessage() . " (SQLSTATE: " . $e->getCode() . ") at " . date('Y-m-d H:i:s'));
        return ['error' => 'Failed to fetch user trips'];
    }
}

function fetchJoinableTrips($user_id) {
    global $pdo;
    try {
        $current_date = date('Y-m-d');
        $user_stmt = $pdo->prepare("SELECT gender FROM users WHERE id = :user_id");
        $user_stmt->execute([':user_id' => $user_id]);
        $user = $user_stmt->fetch(PDO::FETCH_ASSOC);
        $user_gender = $user['gender'] ?? 'any';

        $stmt = $pdo->prepare("
            SELECT st.*, IF(st.ending_date < :current_date AND st.ending_date IS NOT NULL, 'completed', COALESCE(st.status, 'active')) AS status
            FROM solo_trips st
            WHERE st.created_by != :user_id
            AND st.id NOT IN (
                SELECT trip_id FROM trip_members WHERE user_id = :user_id AND status IN ('pending', 'approved')
            )
            AND (st.gender_preference = 'any' OR st.gender_preference = :user_gender)
            AND (st.ending_date IS NULL OR st.ending_date >= :current_date)
        ");
        $stmt->execute([':user_id' => $user_id, ':user_gender' => $user_gender, ':current_date' => $current_date]);
        $joinable_trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("fetchJoinableTrips for user $user_id returned: " . print_r($joinable_trips, true) . " at " . date('Y-m-d H:i:s'));
        return $joinable_trips;
    } catch (PDOException $e) {
        error_log("Database error in fetchJoinableTrips: " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));
        return ['error' => 'Failed to fetch joinable trips'];
    }
}

function fetchPendingJoinRequests($user_id) {
    global $pdo;
    try {
        $current_date = date('Y-m-d');
        $stmt = $pdo->prepare("
            SELECT tm.id AS request_id, tm.trip_id, st.destination, st.travel_date, st.ending_date,
                   u.name AS requester_name, tm.status, tm.joined_at
            FROM trip_members tm
            JOIN solo_trips st ON tm.trip_id = st.id
            JOIN users u ON tm.user_id = u.id
            WHERE st.created_by = :user_id AND tm.status = 'pending'
            AND (st.ending_date IS NULL OR st.ending_date >= :current_date)
        ");
        $stmt->execute([':user_id' => $user_id, ':current_date' => $current_date]);
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("fetchPendingJoinRequests for user $user_id returned: " . print_r($requests, true) . " at " . date('Y-m-d H:i:s'));
        return $requests;
    } catch (PDOException $e) {
        error_log("Database error in fetchPendingJoinRequests: " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));
        return ['error' => 'Failed to fetch pending requests'];
    }
}

function fetchJoinedTrips($user_id) {
    global $pdo;
    try {
        $current_date = date('Y-m-d');
        $stmt = $pdo->prepare("
            SELECT st.*, tm.status, u.name AS creator_name, u.email AS creator_email,
                   IF(st.ending_date < :current_date AND st.ending_date IS NOT NULL, 'completed', COALESCE(st.status, 'active')) AS trip_status
            FROM solo_trips st
            JOIN trip_members tm ON st.id = tm.trip_id
            LEFT JOIN users u ON st.created_by = u.id
            WHERE tm.user_id = :user_id AND tm.status IN ('pending', 'approved')
            AND (st.ending_date IS NULL OR st.ending_date >= :current_date)
        ");
        $stmt->execute([':user_id' => $user_id, ':current_date' => $current_date]);
        $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($trips as &$trip) {
            $trip['members'] = fetchTripMembers($trip['id']);
        }
        error_log("fetchJoinedTrips for user $user_id returned: " . print_r($trips, true) . " at " . date('Y-m-d H:i:s'));
        return $trips;
    } catch (PDOException $e) {
        error_log("Database error in fetchJoinedTrips: " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));
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
            WHERE tm.trip_id = :trip_id AND tm.status IN ('pending', 'approved')
            UNION
            SELECT id, name, email FROM users WHERE id = (SELECT created_by FROM solo_trips WHERE id = :trip_id)
        ");
        $stmt->execute([':trip_id' => $trip_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in fetchTripMembers: " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));
        return [];
    }
}
?>