

<h3>Your Joined Trips</h3>
<?php if (count($joined_trips) > 0): ?>
    <table id="joinedTripsTable">
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
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($joined_trips as $trip): ?>
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
                    <td><?php echo htmlspecialchars($trip['status'] ?? 'Unknown'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No trips joined yet (pending or approved).</p>
<?php endif; ?>