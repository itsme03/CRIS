<?php
require_once 'db/config.php';
require_once 'header.php';

$message = '';
$error = '';
$participants = [];
$workers = [];

try {
    $db = db();
    $participants = $db->query("SELECT id, full_legal_name FROM participants ORDER BY full_legal_name ASC")->fetchAll(PDO::FETCH_ASSOC);
    $workers = $db->query("SELECT id, full_name FROM support_workers ORDER BY full_name ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Failed to fetch participants or workers: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['participant_id']) || empty($_POST['worker_id']) || empty($_POST['service_date'])) {
            throw new Exception("Participant, Worker, and Service Date are required fields.");
        }

        $db = db();
        $sql = "INSERT INTO service_logs (
            participant_id, worker_id, service_date, ndis_line_item, 
            duration_minutes, service_notes, billing_status
        ) VALUES (
            :participant_id, :worker_id, :service_date, :ndis_line_item, 
            :duration_minutes, :service_notes, :billing_status
        )";
        
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':participant_id', $_POST['participant_id'], PDO::PARAM_INT);
        $stmt->bindParam(':worker_id', $_POST['worker_id'], PDO::PARAM_INT);
        $stmt->bindParam(':service_date', $_POST['service_date']);
        $stmt->bindParam(':ndis_line_item', $_POST['ndis_line_item']);
        $stmt->bindParam(':duration_minutes', $_POST['duration_minutes'], PDO::PARAM_INT);
        $stmt->bindParam(':service_notes', $_POST['service_notes']);
        $stmt->bindParam(':billing_status', $_POST['billing_status']);
        
        $stmt->execute();
        
        $message = "Service log successfully added!";
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<header>
    <h1>Log a New Service</h1>
</header>

<?php if ($message): ?>
<div class="feedback success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="feedback error"><?php echo $error; ?></div>
<?php endif; ?>

<form action="log_service.php" method="POST">
    <div class="form-grid">
        <div class="form-group">
            <label for="participant_id">Participant *</label>
            <select id="participant_id" name="participant_id" required>
                <option value="">Select a participant...</option>
                <?php foreach ($participants as $p): ?>
                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['full_legal_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
         <div class="form-group">
            <label for="worker_id">Support Worker *</label>
            <select id="worker_id" name="worker_id" required>
                <option value="">Select a worker...</option>
                <?php foreach ($workers as $w): ?>
                    <option value="<?php echo $w['id']; ?>"><?php echo htmlspecialchars($w['full_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="service_date">Service Date & Time *</label>
            <input type="datetime-local" id="service_date" name="service_date" required>
        </div>
         <div class="form-group">
            <label for="duration_minutes">Duration (minutes)</label>
            <input type="number" id="duration_minutes" name="duration_minutes">
        </div>
        <div class="form-group">
            <label for="ndis_line_item">NDIS Line Item Code</label>
            <input type="text" id="ndis_line_item" name="ndis_line_item" placeholder="e.g., 01_001_0107_5_1">
        </div>
        <div class="form-group">
            <label for="billing_status">Billing Status</label>
            <select id="billing_status" name="billing_status">
                <option value="Pending">Pending</option>
                <option value="Billed">Billed</option>
                <option value="Paid">Paid</option>
            </select>
        </div>
    </div>
    <div class="form-group" style="margin-top: 1.5rem;">
        <label for="service_notes">Brief Service Note/Outcome</label>
        <textarea id="service_notes" name="service_notes" rows="4"></textarea>
    </div>

    <div style="margin-top: 2rem;">
        <button type="submit" class="btn btn-primary">Log Service</button>
        <a href="service_logs.php" class="btn">Cancel</a>
    </div>
</form>

<?php require_once 'footer.php'; ?>