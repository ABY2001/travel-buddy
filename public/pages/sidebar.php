<?php
// File: /public/pages/dashboard.php
session_start();
if (!isset($_SESSION['user']['id'])) {
    header("Location: /public/pages/login.php");
    exit;
}

include '../../api/dashboard.php';

$user_id = $_SESSION['user']['id'];

// Determine active section based on URL parameter or default to 'my-trips'
$active_section = isset($_GET['section']) ? $_GET['section'] : 'my-trips';

$all_trips = fetchUserTrips($user_id);
$joinable_trips = fetchJoinableTrips($user_id);
$pending_requests = fetchPendingJoinRequests($user_id);
$joined_trips = fetchJoinedTrips($user_id);

if (isset($all_trips['error'])) $error_message = $all_trips['error'];
elseif (isset($joinable_trips['error'])) $error_message = $joinable_trips['error'];
elseif (isset($pending_requests['error'])) $error_message = $pending_requests['error'];
elseif (isset($joined_trips['error'])) $error_message = $joined_trips['error'];
else $error_message = '';

include '../includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Travel Buddy</title>
    <link rel="stylesheet" href="../assets/dashboard.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #1a1a1a;
            color: #fff;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background-color: #2c2c2c;
            padding-top: 20px;
            transition: transform 0.3s ease;
        }
        .sidebar a {
            padding: 15px 20px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            display: block;
            transition: background-color 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #444;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .content {
                margin-left: 0;
            }
            .menu-toggle {
                display: block;
                font-size: 24px;
                cursor: pointer;
                padding: 10px;
                background-color: #444;
                color: #fff;
            }
        }
        .logout-btn {
            background-color: #ff4444;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: left;
            margin-top: 20px;
        }
        .logout-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <a href="?section=my-trips" class="<?php echo $active_section === 'my-trips' ? 'active' : ''; ?>">My Trips</a>
        <a href="?section=joined-trips" class="<?php echo $active_section === 'joined-trips' ? 'active' : ''; ?>">Joined Trips</a>
        <a href="?section=joinable-trips" class="<?php echo $active_section === 'joinable-trips' ? 'active' : ''; ?>">Joinable Trips</a>
        <a href="?section=pending-requests" class="<?php echo $active_section === 'pending-requests' ? 'active' : ''; ?>">Pending Requests</a>
        <form action="/api/logout.php" method="post" style="margin: 0;">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <div class="content">
        <?php if ($error_message): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php elseif (isset($_GET['error'])): ?>
            <p class="error-message">
                <?php
                $error = $_GET['error'];
                $error_messages = [
                    'invalid_trip_type' => 'Invalid trip type.',
                    'already_requested' => 'You have already requested to join this trip.',
                    'join_failed' => 'Failed to join the trip. Please try again.',
                    'invalid_trip' => 'Invalid trip ID.',
                    'unauthorized_action' => 'You are not authorized to perform this action.',
                    'update_failed' => 'Failed to update the request. Please try again.',
                    'solo_trip_limit_exceeded' => 'Cannot approve more than one member for a solo trip.'
                ];
                echo htmlspecialchars($error_messages[$error] ?? 'An unknown error occurred.');
                ?>
            </p>
        <?php elseif (isset($_GET['success'])): ?>
            <p class="success-message" id="successMessage"><?php echo htmlspecialchars("Request " . $_GET['success'] . " successfully!"); ?></p>
        <?php endif; ?>

        <?php
        switch ($active_section) {
            case 'my-trips':
                include 'my_trips.php';
                break;
            case 'joined-trips':
                include 'joined_trips.php';
                break;
            case 'joinable-trips':
                include 'joinable_trips.php';
                break;
            case 'pending-requests':
                include 'pending_requests.php';
                break;
            default:
                include 'my_trips.php';
        }
        ?>
    </div>

    <script src="../js/dashboard.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.createElement('div');
        menuToggle.className = 'menu-toggle';
        menuToggle.innerHTML = '☰';
        document.body.insertBefore(menuToggle, sidebar);

        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        const successMessage = document.getElementById('successMessage');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>