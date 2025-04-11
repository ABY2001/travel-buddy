
<h3>Pending Join Requests</h3>
<?php if (count($pending_requests) > 0): ?>
    <table id="pendingRequestsTable">
        <thead>
            <tr>
                <th>Trip Destination</th>
                <th>Travel Date</th>
                <th>Requester</th>
                <th>Requested At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pending_requests as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['destination']); ?></td>
                    <td><?php echo htmlspecialchars($request['travel_date']); ?></td>
                    <td><?php echo htmlspecialchars($request['name']); ?></td>
                    <td><?php echo htmlspecialchars($request['joined_at']); ?></td>
                    <td>
                        <a href="../../api/manage_join_request.php?request_id=<?php echo $request['request_id']; ?>&action=approve" class="join-btn">Approve</a>
                        <a href="../../api/manage_join_request.php?request_id=<?php echo $request['request_id']; ?>&action=reject" class="join-btn" style="background-color: #ff4444;">Reject</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No pending join requests.</p>
<?php endif; ?>