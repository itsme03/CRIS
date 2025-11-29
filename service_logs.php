<?php
require_once 'db/config.php';
require_once 'header.php';

$logs = [];
try {
    $db = db();
    $sql = "SELECT 
                sl.id, 
                sl.service_date, 
                p.full_legal_name AS participant_name, 
                w.full_name AS worker_name, 
                sl.ndis_line_item, 
                sl.duration_minutes,
                sl.billing_status
            FROM 
                service_logs sl
            JOIN 
                participants p ON sl.participant_id = p.id
            JOIN 
                support_workers w ON sl.worker_id = w.id
            ORDER BY 
                sl.service_date DESC";
    $stmt = $db->query($sql);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="feedback error">Error: ' . $e->getMessage() . '</div>';
}

function get_status_chip_class($status) {
    switch ($status) {
        case 'Paid':
            return 'status-paid';
        case 'Billed':
            return 'status-billed';
        case 'Pending':
        default:
            return 'status-pending';
    }
}

?>

<style>
.status-chip {
    padding: 0.3rem 0.8rem;
    border-radius: 16px;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    display: inline-block;
    color: #fff;
}
.status-paid { background-color: #2ecc71; }
.status-billed { background-color: #f1c40f; color: #333; }
.status-pending { background-color: #95a5a6; }
</style>

<header>
    <h1>Service Logs</h1>
    <a href="log_service.php" class="btn btn-primary">Log New Service</a>
</header>

<table>
    <thead>
        <tr>
            <th>Service Date</th>
            <th>Participant</th>
            <th>Support Worker</th>
            <th>Duration (mins)</th>
            <th>NDIS Line Item</th>
            <th>Billing Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($logs)): ?>
            <tr>
                <td colspan="6" style="text-align: center;">No service logs found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($log['service_date']))); ?></td>
                    <td><?php echo htmlspecialchars($log['participant_name']); ?></td>
                    <td><?php echo htmlspecialchars($log['worker_name']); ?></td>
                    <td><?php echo htmlspecialchars($log['duration_minutes']); ?></td>
                    <td><?php echo htmlspecialchars($log['ndis_line_item']); ?></td>
                    <td>
                        <span class="status-chip <?php echo get_status_chip_class($log['billing_status']); ?>">
                            <?php echo htmlspecialchars($log['billing_status']); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>