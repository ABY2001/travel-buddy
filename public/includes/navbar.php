<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/navbar.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <a href="dashboard.php" class="nav-link" data-page="dashboard">Travel Buddy</a>
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php?section=my-trips" class="nav-link <?php echo (isset($_GET['section']) && $_GET['section'] === 'my-trips') || !isset($_GET['section']) ? 'active' : ''; ?>" data-page="my-trips">My Trips</a></li>
            <li><a href="dashboard.php?section=joined-trips" class="nav-link <?php echo isset($_GET['section']) && $_GET['section'] === 'joined-trips' ? 'active' : ''; ?>" data-page="joined-trips">Joined Trips</a></li>
            <li><a href="dashboard.php?section=joinable-trips" class="nav-link <?php echo isset($_GET['section']) && $_GET['section'] === 'joinable-trips' ? 'active' : ''; ?>" data-page="joinable-trips">Joinable Trips</a></li>
            <li><a href="dashboard.php?section=pending-requests" class="nav-link <?php echo isset($_GET['section']) && $_GET['section'] === 'pending-requests' ? 'active' : ''; ?>" data-page="pending-requests">Pending Requests</a></li>
            <li><a href="create_trip.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'create_trip.php' ? 'active' : ''; ?>" data-page="create_trip">Create Trip</a></li>
            <?php if (isset($_SESSION['user'])): ?>
                <li><a href="../../api/logout.php" class="nav-link">Logout</a></li>
            <?php else: ?>
                <li><a href="../public/pages/login.php" class="nav-link">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</body>

</html>