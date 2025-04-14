<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <!-- Use absolute path from root for CSS -->
    <link rel="stylesheet" href="/travel-buddy/assets/admin.css">
</head>
<body>
    <style>
        /* Reset and base style */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a1a1a, #333);
            color: #fff;
            padding: 40px 0;
        }

        /* Container */
        .container {
            width: 90%;
            margin: auto;
            padding: 40px 30px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            backdrop-filter: blur(20px);
            box-shadow: 0 4px 40px rgba(0, 0, 0, 0.3);
        }

        /* Headings */
        h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffcc00;
        }

        /* Navigation Links */
        .links {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .links a {
            color: #ffcc00;
            text-decoration: none;
            font-weight: bold;
            border: 2px solid #ffcc00;
            padding: 10px 20px;
            border-radius: 8px;
            transition: 0.3s ease-in-out;
        }

        .links a:hover {
            background: #ffcc00;
            color: #333;
        }

        /* Flash messages */
        .success-message,
        .error-message {
            text-align: center;
            padding: 12px 18px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 15px;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }

        .success-message {
            background: rgba(76, 175, 80, 0.2);
            color: #4CAF50;
            border: 1px solid #4CAF50;
        }

        .error-message {
            background: rgba(255, 0, 0, 0.2);
            color: #ff4444;
            border: 1px solid #ff4444;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 12px;
            overflow: hidden;
        }

        thead {
            background-color: rgba(255, 204, 0, 0.15);
        }

        thead th {
            padding: 14px 10px;
            text-transform: uppercase;
            font-size: 13px;
            color: #ffcc00;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        tbody td {
            padding: 12px 10px;
            font-size: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.07);
        }

        /* Buttons */
        .join-btn {
            padding: 7px 14px;
            background-color: #ffcc00;
            color: #1a1a1a;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: 0.3s ease;
            text-decoration: none;
        }

        .join-btn:hover {
            background-color: #ffaa00;
            color: #000;
        }

        /* Red variant (for delete) */
        .join-btn.danger {
            background-color: #ff4444;
            color: white;
        }

        .join-btn.danger:hover {
            background-color: #dd3333;
        }

        /* Creator info */
        .creator-tag {
            font-weight: bold;
            color: #ffaa00;
        }
    </style>
    <!-- Container for dashboard -->
    <div class="container">
        <h2>Admin Dashboard</h2>

        <?php if (isset($_GET['msg'])): ?>
            <div class="success-message"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <!-- Navigation Links including Logout -->
        <div class="links">
            <a href="/travel-buddy/admin/admin-dashboard.php">Home</a>
            <a href="/travel-buddy/public/logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
        </div>

        <!-- Trips Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Destination</th>
                    <th>Travel Date</th>
                    <th>Budget ($)</th>
                    <th>Gender Preference</th>
                    <th>Created At</th>
                    <th>Created By</th>
                    <th>Buddy ID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once '../config/db.php';
                try {
                    $stmt = $pdo->query("SELECT * FROM solo_trips ORDER BY created_at DESC");
                    $trips = $stmt->fetchAll();
                } catch (PDOException $e) {
                    die("Error fetching trips: " . $e->getMessage());
                }
                if (count($trips) > 0):
                    foreach ($trips as $trip):
                        ?>
                        <tr>
                            <td><?= $trip['id'] ?></td>
                            <td><?= htmlspecialchars($trip['destination']) ?></td>
                            <td><?= $trip['travel_date'] ?></td>
                            <td><?= $trip['budget'] ?></td>
                            <td><?= ucfirst($trip['gender_preference']) ?></td>
                            <td><?= $trip['created_at'] ?></td>
                            <td><span class="creator-tag"><?= $trip['created_by'] ?></span></td>
                            <td><?= $trip['buddy_id'] ?? 'N/A' ?></td>
                            <td>
                                <form action="/travel-buddy/api/delete_trip.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this trip?');">
                                    <input type="hidden" name="trip_id" value="<?= $trip['id'] ?>">
                                    <button type="submit" class="join-btn danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach;
                else:
                    ?>
                    <tr><td colspan="9" style="text-align: center;">No trips found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Users Table -->
        <h2>Registered Users</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT id, name, email, gender FROM users ORDER BY id ASC");
                    $users = $stmt->fetchAll();
                } catch (PDOException $e) {
                    die("Error fetching users: " . $e->getMessage());
                }
                if (count($users) > 0):
                    foreach ($users as $user):
                        ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= ucfirst($user['gender']) ?? 'N/A' ?></td>
                        </tr>
                    <?php endforeach;
                else:
                    ?>
                    <tr><td colspan="4" style="text-align: center;">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>