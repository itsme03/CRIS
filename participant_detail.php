<?php
require_once 'db/config.php';
require_once 'header.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    echo '<div class="feedback error">Invalid participant ID.</div>';
    require_once 'footer.php';
    exit;
}

$participant_id = $_GET['id'];
$participant = null;

try {
    $db = db();
    $stmt = $db->prepare("SELECT * FROM participants WHERE id = :id");
    $stmt->bindParam(':id', $participant_id, PDO::PARAM_INT);
    $stmt->execute();
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="feedback error">Error: ' . $e->getMessage() . '</div>';
}

if (!$participant) {
    echo '<div class="feedback error">Participant not found.</div>';
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
    <h1><?php echo htmlspecialchars($participant['full_legal_name']); ?></h1>
    <div class="detail-actions">
        <a href="edit_participant.php?id=<?php echo $participant['id']; ?>" class="btn btn-primary">Edit</a>
        <form action="delete_participant.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this participant? This cannot be undone.');">
            <input type="hidden" name="id" value="<?php echo $participant['id']; ?>">
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
        <a href="participants.php" class="btn">Back to List</a>
    </div>
</div>

<div class="detail-grid">
    <div class="detail-section">
        <h2>Participant Details</h2>
        <?php render_detail_item('NDIS Number', $participant['ndis_participant_number']); ?>
        <?php render_detail_item('Date of Birth', date("d M Y", strtotime($participant['date_of_birth']))); ?>
        <?php render_detail_item('Contact Method', $participant['preferred_contact_method']); ?>
    </div>
    <div class="detail-section">
        <h2>Contact Info</h2>
        <?php render_detail_item('Phone', $participant['primary_phone']); ?>
        <?php render_detail_item('Email', $participant['email']); ?>
        <?php render_detail_item('Address', $participant['address']); ?>
        <?php render_detail_item('Emergency Contact', $participant['emergency_contact_name']); ?>
        <?php render_detail_item('Emergency Phone', $participant['emergency_contact_phone']); ?>
    </div>
    <div class="detail-section">
        <h2>NDIS Plan</h2>
        <?php render_detail_item('Plan Start', date("d M Y", strtotime($participant['ndis_plan_start_date']))); ?>
        <?php render_detail_item('Plan End', date("d M Y", strtotime($participant['ndis_plan_end_date']))); ?>
        <?php render_detail_item('Plan Manager', $participant['plan_manager_name']); ?>
        <?php render_detail_item('Manager Contact', $participant['plan_manager_contact']); ?>
        <?php render_detail_item('Total Budget', $participant['ndis_funding_budget_total'], true); ?>
    </div>
</div>

<div class="detail-section" style="margin-top: 2rem;">
    <h2>Disability, Needs & Risks</h2>
    <?php render_detail_area('Primary Disability', $participant['primary_disability']); ?>
    <?php render_detail_area('Support Needs', $participant['support_needs_summary']); ?>
    <?php render_detail_area('Communication Aids', $participant['communication_aids_methods']); ?>
    <?php render_detail_area('Behaviours of Concern', $participant['behaviours_of_concern']); ?>
    <?php render_detail_area('Risk Summary', $participant['risk_assessment_summary']); ?>
    <?php render_detail_area('Safety Plan', $participant['safety_plan']); ?>
</div>

<div class="detail-section" style="margin-top: 2rem;">
    <h2>Intake Notes</h2>
    <?php render_detail_area('Raw notes from intake', $participant['intake_notes']); ?>
</div>

<?php require_once 'footer.php'; ?>