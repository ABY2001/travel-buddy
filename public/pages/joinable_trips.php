
<h3>Joinable Solo Trips</h3>
<?php if (count($joinable_trips) > 0): ?>
    <table id="joinableTripsTable">
        <thead>
            <tr>
                <th>Trip Type</th>
                <th>Destination</th>
                <th>Travel Date</th>
                <th>Budget</th>
                <th>Gender Preference</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($joinable_trips as $trip): ?>
                <tr>
                    <td><?php echo htmlspecialchars($trip['trip_type']); ?></td>
                    <td><?php echo htmlspecialchars($trip['destination']); ?></td>
                    <td><?php echo htmlspecialchars($trip['travel_date']); ?></td>
                    <td><?php echo htmlspecialchars($trip['budget']); ?></td>
                    <td><?php echo htmlspecialchars($trip['gender_preference']); ?></td>
                    <td><?php echo htmlspecialchars($trip['created_at']); ?></td>
                    <td>
                        <a href="../../api/join_trip.php?trip_id=<?php echo $trip['id']; ?>&type=solo" class="join-btn">Join</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No joinable solo trips available.</p>
<?php endif; ?>