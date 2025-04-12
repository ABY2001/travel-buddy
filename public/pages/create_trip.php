<?php include '../includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Trip</title>
    <link rel="stylesheet" href="../assets/create_trip.css">
    <style>
        .message {
            padding: 10px;
            margin-bottom: 10px;
            display: none;
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<div class="container">
    <div class="section create">
        <img src="../assets/solo-travel.jpg" alt="Create a Trip" class="trip-image">
        <button class="btn" id="openCreateModal">Create a Trip</button>
    </div>

    <div class="section join">
        <img src="../assets/group-travel.jpg" alt="Create a Squad of 4 People" class="trip-image">
        <button class="btn" id="openGroupModal">Create a Squad of 4 People</button>
    </div>
</div>

<!-- Create Trip Modal -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeCreateModal">×</span>
        <h2>Create a Trip</h2>
        <div class="message success" id="createSuccess"></div>
        <div class="message error" id="createError"></div>
        <form id="soloTripForm" action="../../api/trips.php" method="POST">
            <input type="hidden" name="trip_type" value="solo">
            <label for="createDest">Destination:</label>
            <input type="text" id="createDest" name="destination" required>

            <label for="createDate">Travel Date:</label>
            <input type="date" id="createDate" name="travel_date" required>

            <label for="createBudget">Budget (in USD):</label>
            <input type="number" id="createBudget" name="budget" min="0" step="0.01" required>

            <label for="createGenderPref">Partner Gender Preference:</label>
            <select id="createGenderPref" name="gender_preference" required>
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="any">Any</option>
            </select>

            <button type="submit">Create Trip</button>
        </form>
    </div>
</div>

<!-- Group Travel Modal -->
<div id="groupModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeGroupModal">×</span>
        <h2>Create a Squad of 4 People</h2>
        <div class="message success" id="groupSuccess"></div>
        <div class="message error" id="groupError"></div>
        <form id="groupTripForm" action="../../api/trips.php" method="POST">
            <input type="hidden" name="trip_type" value="group">
            <label for="groupDest">Destination:</label>
            <input type="text" id="groupDest" name="destination" required>

            <label for="groupDate">Travel Date:</label>
            <input type="date" id="groupDate" name="travel_date" required>

            <label for="groupBudget">Budget per Person (in USD):</label>
            <input type="number" id="groupBudget" name="budget" min="0" step="0.01" required>

            <label for="groupGenderPref">Partners' Gender Preference:</label>
            <select id="groupGenderPref" name="gender_preference" required>
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="any">Any</option>
            </select>

            <label for="groupMembers">Total Members:</label>
            <input type="number" id="groupMembers" name="total_members" value="4" readonly>

            <button type="submit">Create Trip</button>
        </form>
    </div>
</div>

<script src="../js/create_trip.js"></script>
<script>
    // Handle form submission feedback
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const createError = document.getElementById('createError');
        const createSuccess = document.getElementById('createSuccess');
        const groupError = document.getElementById('groupError');
        const groupSuccess = document.getElementById('groupSuccess');

        // Check for success or error from solo trip
        if (urlParams.get('success') === 'trip_created') {
            createSuccess.textContent = 'Trip created successfully!';
            createSuccess.style.display = 'block';
            setTimeout(() => createSuccess.style.display = 'none', 3000);
        } else if (urlParams.get('error')) {
            createError.textContent = decodeURIComponent(urlParams.get('error'));
            createError.style.display = 'block';
            setTimeout(() => createError.style.display = 'none', 5000);
        }

        // Check for success or error from group trip
        if (urlParams.get('success') === 'trip_created') {
            groupSuccess.textContent = 'Squad created successfully!';
            groupSuccess.style.display = 'block';
            setTimeout(() => groupSuccess.style.display = 'none', 3000);
        } else if (urlParams.get('error')) {
            groupError.textContent = decodeURIComponent(urlParams.get('error'));
            groupError.style.display = 'block';
            setTimeout(() => groupError.style.display = 'none', 5000);
        }
    });
</script>
</body>
</html>