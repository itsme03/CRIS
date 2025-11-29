<?php
require_once 'db/config.php';
require_once 'header.php';

$plan_alerts = [];
$screening_alerts = [];
$first_aid_alerts = [];
$error = '';

try {
    $db = db();
    $ninety_days_from_now = date('Y-m-d', strtotime('+90 days'));

    // NDIS Plan Reviews
    $plan_stmt = $db->prepare("SELECT id, full_legal_name, ndis_plan_end_date FROM participants WHERE ndis_plan_end_date <= :end_date AND ndis_plan_end_date >= CURDATE() ORDER BY ndis_plan_end_date ASC");
    $plan_stmt->bindParam(':end_date', $ninety_days_from_now);
    $plan_stmt->execute();
    $plan_alerts = $plan_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Worker Screening Expiries
    $screening_stmt = $db->prepare("SELECT id, full_name, ndis_worker_screening_expiry FROM support_workers WHERE ndis_worker_screening_expiry <= :end_date AND ndis_worker_screening_expiry >= CURDATE() ORDER BY ndis_worker_screening_expiry ASC");
    $screening_stmt->bindParam(':end_date', $ninety_days_from_now);
    $screening_stmt->execute();
    $screening_alerts = $screening_stmt->fetchAll(PDO::FETCH_ASSOC);

    // First Aid Expiries
    $fa_stmt = $db->prepare("SELECT id, full_name, first_aid_expiry FROM support_workers WHERE first_aid_expiry <= :end_date AND first_aid_expiry >= CURDATE() ORDER BY first_aid_expiry ASC");
    $fa_stmt->bindParam(':end_date', $ninety_days_from_now);
    $fa_stmt->execute();
    $first_aid_alerts = $fa_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}

function get_days_until_badge($date) {
    $now = time();
    $your_date = strtotime($date);
    $datediff = $your_date - $now;
    $days = round($datediff / (60 * 60 * 24));
    
    $class = ' ';
    if ($days < 0) $class = 'expired';
    elseif ($days <= 30) $class = 'urgent';
    elseif ($days <= 60) $class = 'soon';
    else $class = 'safe';

    $text = ($days < 0) ? "Expired" : "{$days} days";

    return "<span class=\"days-badge {$class}\">{$text}</span>";
}

?>
<style>
.alert-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}
.alert-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.alert-card h2 {
    margin-bottom: 1.5rem;
    font-size: 1.4rem;
}
.alert-list li {
    list-style: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #e0e0e0;
}
.alert-list li:last-child { border-bottom: none; }

.days-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 16px;
    font-weight: 600;
    font-size: 0.8rem;
    color: #fff;
}
.days-badge.expired { background-color: #e74c3c; }
.days-badge.urgent { background-color: #f39c12; }
.days-badge.soon { background-color: #3498db; }
.days-badge.safe { background-color: #2ecc71; }

</style>

<header>
    <h1>Compliance Dashboard</h1>
</header>

<?php if ($error): ?>
<div class="feedback error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="alert-grid">
    <div class="alert-card">
        <h2>NDIS Plan Reviews (Next 90 Days)</h2>
        <ul class="alert-list">
            <?php if (empty($plan_alerts)): ?>
                <li>No upcoming plan reviews.</li>
            <?php else: ?>
                <?php foreach ($plan_alerts as $alert): ?>
                    <li>
                        <a href="participant_detail.php?id=<?php echo $alert['id']; ?>"><?php echo htmlspecialchars($alert['full_legal_name']); ?></a>
                        <div><?php echo htmlspecialchars(date("d M Y", strtotime($alert['ndis_plan_end_date']))); ?> &nbsp; <?php echo get_days_until_badge($alert['ndis_plan_end_date']); ?></div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
    
    <div class="alert-card">
        <h2>Worker Screening Expiries</h2>
        <ul class="alert-list">
            <?php if (empty($screening_alerts)): ?>
                <li>No upcoming screening expiries.</li>
            <?php else: ?>
                <?php foreach ($screening_alerts as $alert): ?>
                    <li>
                        <a href="worker_detail.php?id=<?php echo $alert['id']; ?>"><?php echo htmlspecialchars($alert['full_name']); ?></a>
                        <div><?php echo htmlspecialchars(date("d M Y", strtotime($alert['ndis_worker_screening_expiry']))); ?> &nbsp; <?php echo get_days_until_badge($alert['ndis_worker_screening_expiry']); ?></div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <div class="alert-card">
        <h2>First Aid Certificate Expiries</h2>
        <ul class="alert-list">
             <?php if (empty($first_aid_alerts)): ?>
                <li>No upcoming First Aid expiries.</li>
            <?php else: ?>
                <?php foreach ($first_aid_alerts as $alert): ?>
                    <li>
                       <a href="worker_detail.php?id=<?php echo $alert['id']; ?>"><?php echo htmlspecialchars($alert['full_name']); ?></a>
                       <div><?php echo htmlspecialchars(date("d M Y", strtotime($alert['first_aid_expiry']))); ?> &nbsp; <?php echo get_days_until_badge($alert['first_aid_expiry']); ?></div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php require_once 'footer.php'; ?>