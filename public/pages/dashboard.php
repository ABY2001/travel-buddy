<?php
// File: /public/pages/dashboard.php
session_start();
error_log("Dashboard loaded at " . date('Y-m-d H:i:s') . " with user_id: " . ($_SESSION['user']['id'] ?? 'not set'));
if (!isset($_SESSION['user']['id'])) {
    header("Location: /travel-buddy/public/pages/login.php");
    exit;
}
include '../../api/notifications.php';
include '../../api/dashboard.php';
$notification = getNotification();
$user_id = $_SESSION['user']['id'];

// Determine active section based on URL parameter or default to 'my-trips'
$active_section = isset($_GET['section']) ? $_GET['section'] : 'my-trips';

// Fetch all trips data
$all_trips = fetchUserTrips($user_id);
error_log("Fetched all_trips for user_id $user_id: " . print_r($all_trips, true));
$joinable_trips = fetchJoinableTrips($user_id);
error_log("Fetched joinable_trips for user_id $user_id: " . print_r($joinable_trips, true));
$pending_requests = fetchPendingJoinRequests($user_id);
error_log("Fetched pending_requests for user_id $user_id: " . print_r($pending_requests, true));
$joined_trips = fetchJoinedTrips($user_id);
error_log("Fetched joined_trips for user_id $user_id: " . print_r($joined_trips, true));

if (isset($all_trips['error'])) $error_message = $all_trips['error'];
elseif (isset($joinable_trips['error'])) $error_message = $joinable_trips['error'];
elseif (isset($pending_requests['error'])) $error_message = $pending_requests['error'];
elseif (isset($joined_trips['error'])) $error_message = $joined_trips['error'];
else $error_message = '';

if ($error_message) {
    error_log("Error message set: $error_message");
}

// Convert data to JSON for JavaScript
$all_trips_json = json_encode($all_trips);
$joinable_trips_json = json_encode($joinable_trips);
$joined_trips_json = json_encode($joined_trips);
$pending_requests_json = json_encode($pending_requests);

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
        /* Search Bar */
        .search-bar {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .search-bar input {
            padding: 10px;
            width: 300px;
            border: 2px solid #ffcc00;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s ease;
        }
        .search-bar input[type="date"] {
            margin-left: 10px;
            width: 200px;
        }
        .search-bar input:focus {
            border-color: #ffaa00;
        }
        .search-bar input::placeholder {
            color: #ccc;
        }

        /* Tab Navigation */
        .tab-nav {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .tab-nav a {
            color: #ffcc00;
            font-weight: 600;
            text-decoration: none;
            padding: 10px 20px;
            border: 2px solid #ffcc00;
            border-radius: 8px;
            transition: 0.3s ease-in-out;
        }
        .tab-nav a.active {
            background: #ffcc00;
            color: #333;
        }
        .tab-nav a:hover {
            background: #ffaa00;
            color: #000;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }

        /* Dynamic Table Styles */
        .dynamic-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0 40px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        .dynamic-table thead {
            background: rgba(255, 204, 0, 0.15);
        }
        .dynamic-table thead th {
            padding: 14px 10px;
            text-transform: uppercase;
            font-size: 14px;
            color: #ffcc00;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .dynamic-table tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 15px;
        }
        .dynamic-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.07);
        }

        /* Notification Styles */
        .notification {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello, <?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Traveler'); ?>! Explore Your Travel Buddy Journey</h2>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by location..." onkeyup="filterTables()">
            <input type="date" id="searchDate" onchange="filterTables()">
        </div>

        <!-- Display Notification -->
        <?php if ($notification): ?>
            <div class="notification <?php echo htmlspecialchars($notification['type']); ?>">
                <?php echo htmlspecialchars($notification['message']); ?>
            </div>
        <?php endif; ?>

        <!-- API Error Message -->
        <?php if ($error_message): ?>
            <div class="notification error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Tab Navigation -->
        <div class="tab-nav">
            <a href="?section=my-trips" class="<?php echo $active_section === 'my-trips' ? 'active' : ''; ?>">My Trips</a>
            <a href="?section=joined-trips" class="<?php echo $active_section === 'joined-trips' ? 'active' : ''; ?>">Joined Trips</a>
            <a href="?section=joinable-trips" class="<?php echo $active_section === 'joinable-trips' ? 'active' : ''; ?>">Joinable Trips</a>
            <a href="?section=pending-requests" class="<?php echo $active_section === 'pending-requests' ? 'active' : ''; ?>">Pending Requests</a>
            <a href="?section=previous-trips" class="<?php echo $active_section === 'previous-trips' ? 'active' : ''; ?>">Previous Trips</a>
        </div>

        <!-- Tab Content with Dynamic Tables -->
        <div class="tab-content <?php echo $active_section === 'my-trips' ? 'active' : ''; ?>" id="my-trips">
            <table class="dynamic-table" id="myTripsTable"></table>
        </div>
        <div class="tab-content <?php echo $active_section === 'joined-trips' ? 'active' : ''; ?>" id="joined-trips">
            <table class="dynamic-table" id="joinedTripsTable"></table>
        </div>
        <div class="tab-content <?php echo $active_section === 'joinable-trips' ? 'active' : ''; ?>" id="joinable-trips">
            <table class="dynamic-table" id="joinableTripsTable"></table>
        </div>
        <div class="tab-content <?php echo $active_section === 'pending-requests' ? 'active' : ''; ?>" id="pending-requests">
            <table class="dynamic-table" id="pendingRequestsTable"></table>
        </div>
        <div class="tab-content <?php echo $active_section === 'previous-trips' ? 'active' : ''; ?>" id="previous-trips">
            <table class="dynamic-table" id="previousTripsTable"></table>
        </div>
    </div>

    <script src="../js/dashboard.js"></script>
    <script>
        // Parse JSON data from PHP
        const allTrips = <?php echo $all_trips_json; ?> || [];
        const joinableTrips = <?php echo $joinable_trips_json; ?> || [];
        const joinedTrips = <?php echo $joined_trips_json; ?> || [];
        const pendingRequests = <?php echo $pending_requests_json; ?> || [];

        console.log('All Trips:', allTrips);
        console.log('Joined Trips:', joinedTrips);
        console.log('Joinable Trips:', joinableTrips);
        console.log('Pending Requests:', pendingRequests);

        // Function to populate tables
        function populateTable(tableId, data, columnMapping) {
            const table = document.getElementById(tableId);
            table.innerHTML = ''; // Clear existing content

            if (!data || data.length === 0) {
                const tbody = document.createElement('tbody');
                const row = document.createElement('tr');
                const td = document.createElement('td');
                td.setAttribute('colspan', Object.keys(columnMapping).length);
                td.textContent = 'No trips found.';
                td.style.textAlign = 'center';
                row.appendChild(td);
                tbody.appendChild(row);
                table.appendChild(tbody);
                return;
            }

            // Create header
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            for (let header in columnMapping) {
                const th = document.createElement('th');
                th.textContent = header;
                headerRow.appendChild(th);
            }
            thead.appendChild(headerRow);
            table.appendChild(thead);

            // Create body
            const tbody = document.createElement('tbody');
            const currentDate = new Date('2025-04-13'); // Current date
            data.forEach(item => {
                const row = document.createElement('tr');
                for (let header in columnMapping) {
                    const td = document.createElement('td');
                    const key = columnMapping[header];
                    if (header === 'Members') {
                        const membersDiv = document.createElement('div');
                        if (item[key] && Array.isArray(item[key])) {
                            item[key].forEach(member => {
                                const memberDiv = document.createElement('div');
                                memberDiv.textContent = `${member.name} (${member.email || 'No email'})`;
                                membersDiv.appendChild(memberDiv);
                            });
                        }
                        if (!item[key] || item[key].length === 0) {
                            membersDiv.textContent = 'No members yet';
                        }
                        td.appendChild(membersDiv);
                    } else if (header === 'Creator') {
                        td.innerHTML = `<span class="creator-tag">Creator: ${item[key + '_name'] || 'Unknown'} (${item[key + '_email'] || 'No email'})</span>`;
                    } else if (header === 'Requester') {
                        td.innerHTML = `${item[key] || 'Unknown'}<br><span style="color: #ffaa00; font-size: 12px;">(${item['requester_email'] || 'No email'})</span>`;
                    } else if (header === 'Action' && tableId === 'joinableTripsTable' && item.id) {
                        td.innerHTML = `<a href="/travel-buddy/api/join_trip.php?trip_id=${item.id}&type=solo" class="join-btn">Join</a>`;
                    } else if (header === 'Action' && tableId === 'pendingRequestsTable' && item.request_id) {
                        td.innerHTML = `
                            <a href="/travel-buddy/api/manage_join_request.php?request_id=${item.request_id}&action=approve" class="join-btn">Approve</a>
                            <a href="/travel-buddy/api/manage_join_request.php?request_id=${item.request_id}&action=reject" class="join-btn" style="background-color: #ff4444;">Reject</a>
                        `;
                    } else if (header === 'Action' && tableId === 'myTripsTable' && item.id) {
                        const tripStartDate = new Date(item['travel_date']);
                        if (tripStartDate > currentDate) {
                            td.innerHTML = `<a href="/travel-buddy/api/delete_trip_user.php?trip_id=${item.id}" class="join-btn" style="background-color: #ff4444;" onclick="return confirm('Are you sure you want to delete this trip?')">Delete</a>`;
                        } else {
                            td.textContent = 'Cannot delete (trip started)';
                            td.style.color = '#ff4444';
                        }
                    } else {
                        td.textContent = item[key] !== undefined ? item[key] : 'N/A';
                    }
                    row.appendChild(td);
                }
                tbody.appendChild(row);
            });
            table.appendChild(tbody);
        }

        // Column mappings (header: data key) with new fields
        const myTripsColumns = {
            'Destination': 'destination',
            'Travel Date': 'travel_date',
            'Ending Date': 'ending_date',
            'Budget': 'budget',
            'Gender Preference': 'gender_preference',
            'Created At': 'created_at',
            'Status': 'status',
            'Creator': 'creator',
            'Members': 'members',
            'Action': 'id' // Added for delete button
        };
        const joinedTripsColumns = {
            'Destination': 'destination',
            'Travel Date': 'travel_date',
            'Ending Date': 'ending_date',
            'Budget': 'budget',
            'Gender Preference': 'gender_preference',
            'Created At': 'created_at',
            'Status': 'trip_status',
            'Creator': 'creator',
            'Members': 'members'
        };
        const joinableTripsColumns = {
            'Destination': 'destination',
            'Travel Date': 'travel_date',
            'Ending Date': 'ending_date',
            'Budget': 'budget',
            'Gender Preference': 'gender_preference',
            'Created At': 'created_at',
            'Status': 'status',
            'Action': 'id'
        };
        const pendingRequestsColumns = {
            'Trip Destination': 'destination',
            'Travel Date': 'travel_date',
            'Ending Date': 'ending_date',
            'Requester': 'requester_name',
            'Requested At': 'joined_at',
            'Action': 'request_id'
        };
        const previousTripsColumns = {
            'Destination': 'destination',
            'Travel Date': 'travel_date',
            'Ending Date': 'ending_date',
            'Budget': 'budget',
            'Gender Preference': 'gender_preference',
            'Created At': 'created_at',
            'Status': 'status',
            'Creator': 'creator',
            'Members': 'members'
        };

        // Initial population
        populateTable('myTripsTable', allTrips, myTripsColumns);
        populateTable('joinedTripsTable', joinedTrips, joinedTripsColumns);
        populateTable('joinableTripsTable', joinableTrips, joinableTripsColumns);
        populateTable('pendingRequestsTable', pendingRequests, pendingRequestsColumns);
        populateTable('previousTripsTable', allTrips.filter(trip => new Date(trip.ending_date) < new Date('2025-04-13')), previousTripsColumns);

        // Frontend search function
        function filterTables() {
            const locationInput = document.getElementById('searchInput').value.toLowerCase();
            const startDateInput = document.getElementById('searchDate').value;
            const endDateInput = document.getElementById('endDateFilter')?.value;

            const filterTrip = (trip) => {
                const destinationMatch = trip.destination && trip.destination.toLowerCase().includes(locationInput);
                const startDateMatch = !startDateInput || new Date(trip.travel_date) >= new Date(startDateInput);
                const endDateMatch = !endDateInput || (trip.ending_date && new Date(trip.ending_date) <= new Date(endDateInput));
                return destinationMatch && startDateMatch && endDateMatch;
            };

            let myTripsData = allTrips.filter(trip => new Date(trip.ending_date) >= new Date('2025-04-13')).filter(filterTrip);
            populateTable('myTripsTable', myTripsData, myTripsColumns);

            let joinedTripsData = joinedTrips.filter(trip => new Date(trip.ending_date) >= new Date('2025-04-13')).filter(filterTrip);
            populateTable('joinedTripsTable', joinedTripsData, joinedTripsColumns);

            let joinableTripsData = joinableTrips.filter(filterTrip);
            populateTable('joinableTripsTable', joinableTripsData, joinableTripsColumns);

            let pendingRequestsData = pendingRequests.filter((request) => {
                const destinationMatch = request.destination && request.destination.toLowerCase().includes(locationInput);
                const startDateMatch = !startDateInput || new Date(request.travel_date) >= new Date(startDateInput);
                const endDateMatch = !endDateInput || (request.ending_date && new Date(request.ending_date) <= new Date(endDateInput));
                return destinationMatch && startDateMatch && endDateMatch;
            });
            populateTable('pendingRequestsTable', pendingRequestsData, pendingRequestsColumns);

            let previousTripsData = allTrips.filter(trip => new Date(trip.ending_date) < new Date('2025-04-13')).filter(filterTrip);
            populateTable('previousTripsTable', previousTripsData, previousTripsColumns);
        }

        // Auto-hide notification after 5 seconds
        document.addEventListener('DOMContentLoaded', () => {
            const notification = document.querySelector('.notification');
            if (notification) {
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 5000);
            }
        });
    </script>
</body>
</html>