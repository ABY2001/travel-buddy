<?php
session_start(); 
include '../includes/navbar.php'; 
?>

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

        .error-message {
            color: red;
            font-size: 0.85em;
            margin-top: 2px;
            display: block;
        }

    </style>
</head>
<body>
<div class="container">
    <div class="section create">
        <img src="../assets/solo-travel.jpg" alt="Create a Trip" class="trip-image">
        <button class="btn" id="openCreateModal">Create a Trip</button>
    </div>

    <!-- <div class="section join">
        <img src="../assets/group-travel.jpg" alt="Create a Squad of 4 People" class="trip-image">
        <button class="btn" id="openGroupModal">Create a Squad of 4 People</button>
    </div> -->
</div>

<!-- Create Trip Modal -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeCreateModal">×</span>
        <h2>Create a Trip</h2>
        <div class="message success" id="createSuccess"></div>
        <div class="message error" id="createError"></div>
        <form id="soloTripForm" action="../../api/trips.php" method="POST" novalidate>
            <input type="hidden" name="trip_type" value="solo">
            <label for="createDest">Destination:</label>
            <input type="text" id="createDest" name="destination" required>
            <span class="error-message" id="createDestError"></span>

            <label for="createDate">Starting Date:</label>
            <input type="date" id="createDate" name="travel_date" required>
            <span class="error-message" id="createDateError"></span>

            <label for="endingDate">Ending Date:</label>
            <input type="date" id="endingDate" name="ending_date" required>
            <span class="error-message" id="endingDateError"></span>

            <label for="createBudget">Travel Budget :</label>
            <input type="number" id="createBudget" name="budget" min="0" step="0.01" required>
            <span class="error-message" id="createBudgetError"></span>

            <label for="createGenderPref">Partner Gender Preference:</label>
            <select id="createGenderPref" name="gender_preference" required>
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="any">Any</option>
            </select>
            <span class="error-message" id="createGenderPrefError"></span>

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
    document.addEventListener("DOMContentLoaded", function () {
    const createModal = document.getElementById("createModal");
    const groupModal = document.getElementById("groupModal");
    const openCreateModalBtn = document.getElementById("openCreateModal");
    const openGroupModalBtn = document.getElementById("openGroupModal");
    const closeCreateModalBtn = document.getElementById("closeCreateModal");
    const closeGroupModalBtn = document.getElementById("closeGroupModal");

    // Modal open/close logic
    if (openCreateModalBtn) {
        openCreateModalBtn.addEventListener("click", () => {
            createModal.style.display = "flex";
        });
    }

    if (closeCreateModalBtn) {
        closeCreateModalBtn.addEventListener("click", () => {
            createModal.style.display = "none";
        });
    }

    if (openGroupModalBtn) {
        openGroupModalBtn.addEventListener("click", () => {
            groupModal.style.display = "flex";
        });
    }

    if (closeGroupModalBtn) {
        closeGroupModalBtn.addEventListener("click", () => {
            groupModal.style.display = "none";
        });
    }

    window.addEventListener("click", (event) => {
        if (event.target === createModal) {
            createModal.style.display = "none";
        }
        if (event.target === groupModal) {
            groupModal.style.display = "none";
        }
    });

    // Helper to show/hide error spans
    function showError(id, message) {
        const span = document.getElementById(id);
        if (span) span.textContent = message;
    }

    function clearAllErrors(ids) {
        ids.forEach(id => showError(id, ""));
    }

    // SOLO TRIP VALIDATION
    const soloForm = document.getElementById("soloTripForm");
    if (soloForm) {
        soloForm.addEventListener("submit", function (e) {
            const ids = ["createDestError", "createDateError", "endingDateError", "createBudgetError", "createGenderPrefError"];
            clearAllErrors(ids);
            let isValid = true;

            const dest = document.getElementById("createDest").value.trim();
            const start = document.getElementById("createDate").value;
            const end = document.getElementById("endingDate").value;
            const budget = document.getElementById("createBudget").value;
            const gender = document.getElementById("createGenderPref").value;

            if (!dest) {
                showError("createDestError", "Destination is required.");
                isValid = false;
            }


            if (!start) {
                showError("createDateError", "Travel date is required.");
                isValid = false;
            }


            if (!end) {
                showError("endingDateError", "Ending date is required.");
                isValid = false;
            } else if (start && new Date(end) <= new Date(start)) {
                showError("endingDateError", "Ending date must be after travel date.");
                isValid = false;
            }


            if (!budget || parseFloat(budget) <= 0) {
                showError("createBudgetError", "Please enter a valid budget.");
                isValid = false;
            }


            if (!gender) {
                showError("createGenderPrefError", "Please select a gender preference.");
                isValid = false;
            }

            if (!isValid) e.preventDefault();
        
        });
    }

    // Add groupForm validation here if needed...
});

</script>
</body>
</html>