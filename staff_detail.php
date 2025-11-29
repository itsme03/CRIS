<?php
require_once 'db/config.php';
require_once 'header.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    echo '<div class="feedback error">Invalid staff ID.</div>';
    require_once 'footer.php';
    exit;
}

$staff_id = $_GET['id'];
$staff = null;

try {
    $db = db();
    $stmt = $db->prepare("SELECT * FROM care_staff WHERE id = :id");
    $stmt->bindParam(':id', $staff_id, PDO::PARAM_INT);
    $stmt->execute();
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="feedback error">Error: ' . $e->getMessage() . '</div>';
}

if (!$staff) {
    echo '<div class="feedback error">Care staff member not found.</div>';
    require_once 'footer.php';
    exit;
}

function render_detail_item($label, $value, $is_currency = false) {
    if (!empty($value)) {
        $display_value = htmlspecialchars($value);
        if ($is_currency) {
            $display_value = '$' . number_format((float)$value, 2);
        }
        echo "<div class='detail-item'><span class='label'>{$label}</span><span class='value'>{$display_value}</span></div>";
    }
}

function render_detail_area($label, $value) {
    if (!empty($value)) {
        echo "<div class='detail-area'><span class='label'>{$label}</span><pre class='value'>" . htmlspecialchars($value) . "</pre></div>";
    }
}
?>
<style>
    .detail-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .detail-actions { display: flex; gap: 1rem; }
    .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
    .detail-section { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .detail-section h2 { font-size: 1.3rem; margin-bottom: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 1rem; }
    .detail-item { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f5f5f5; }
    .detail-item:last-child { border-bottom: none; }
    .detail-item .label { font-weight: 600; color: #555; }
    .detail-area { margin-top: 1rem; }
    .detail-area .label { font-weight: 600; display: block; margin-bottom: 0.5rem; }
    .detail-area pre { background: #f9f9f9; padding: 1rem; border-radius: 8px; white-space: pre-wrap; word-wrap: break-word; font-family: inherit; }
</style>

<div class="detail-header">
    <h1><?php echo htmlspecialchars($staff['full_name']); ?></h1>
    <div class="detail-actions">
        <a href="edit_staff.php?id=<?php echo $staff['id']; ?>" class="btn btn-primary">Edit</a>
        <form action="delete_staff.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this staff member? This cannot be undone.');">
            <input type="hidden" name="id" value="<?php echo $staff['id']; ?>">
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
        <a href="care_staff.php" class="btn">Back to List</a>
    </div>
</div>

<div class="detail-grid">
    <div class="detail-section">
        <h2>Personal & Contact</h2>
        <?php render_detail_item('Full Name', $staff['full_name']); ?>
        <?php render_detail_area('Contact Info', $staff['contact_info']); ?>
    </div>
    <div class="detail-section">
        <h2>Compliance</h2>
        <?php render_detail_item('Screening Number', $staff['ndis_worker_screening_number']); ?>
        <?php render_detail_item('Screening Expiry', date("d M Y", strtotime($staff['ndis_worker_screening_expiry']))); ?>
        <?php render_detail_item('First Aid Expiry', date("d M Y", strtotime($staff['first_aid_expiry']))); ?>
    </div>
    <div class="detail-section">
        <h2>Work Details</h2>
        <?php render_detail_item('Hourly Rate', $staff['hourly_rate'], true); ?>
        <?php render_detail_area('Qualifications', $staff['qualifications']); ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>