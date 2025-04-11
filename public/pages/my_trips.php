
<h3>My Trips</h3>
<?php if (count($all_trips) > 0): ?>
    <table id="tripsTable">
        <thead>
            <tr>
                <th data-sort="trip_type">Trip Type</th>
                <th data-sort="destination">Destination</th>
                <th data-sort="travel_date">Travel Date</th>
                <th data-sort="budget">Budget</th>
                <th data-sort="gender_preference">Gender Preference</th>
                <th data-sort="created_at">Created At</th>
                <th>Creator</th>
                <th>Members</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($all_trips as $trip): ?>
                <tr>
                    <td><?php echo htmlspecialchars($trip['trip_type']); ?></td>
                    <td><?php echo htmlspecialchars($trip['destination']); ?></td>
                    <td><?php echo htmlspecialchars($trip['travel_date']); ?></td>
                    <td><?php echo htmlspecialchars($trip['budget']); ?></td>
                    <td><?php echo htmlspecialchars($trip['gender_preference']); ?></td>
                    <td><?php echo isset($trip['created_at']) ? htmlspecialchars($trip['created_at']) : 'N/A'; ?></td>
                    <td>
                        <span class="creator-tag">Creator: <?php echo htmlspecialchars($trip['creator_name']) . ' (' . htmlspecialchars($trip['creator_email']) . ')'; ?></span>
                    </td>
                    <td>
                        <?php 
                        $members = $trip['members'] ?? [];
                        foreach ($members as $member): ?>
                            <div>
                                <?php echo htmlspecialchars($member['name'] ?? 'Unknown') . ' (' . htmlspecialchars($member['email'] ?? 'Unknown') . ')'; ?>
                            </div>
                        <?php endforeach; 
                        if (empty($members)) echo '<div>No members yet</div>';
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No trips found. Start by creating a new trip!</p>
<?php endif; ?>

<a href="create_trip.php" class="join-btn" style="display: inline-block; margin-top: 20px;">Create a Trip</a>