<?php
require_once 'db/config.php';
require_once 'header.php';

$message = '';
$error = '';
$clients = [];
$care_staff = [];

try {
    $db = db();
    $clients = $db->query("SELECT id, full_legal_name FROM clients ORDER BY full_legal_name ASC")->fetchAll(PDO::FETCH_ASSOC);
    $care_staff = $db->query("SELECT id, full_name FROM care_staff ORDER BY full_name ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Failed to fetch clients or care staff: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['client_id']) || empty($_POST['staff_id']) || empty($_POST['service_date'])) {
            throw new Exception("Client, Care Staff, and Service Date are required fields.");
        }

        $db = db();
        $sql = "INSERT INTO bookings (
            client_id, staff_id, service_date, ndis_line_item, 
            duration_minutes, service_notes, billing_status
        ) VALUES (
            :client_id, :staff_id, :service_date, :ndis_line_item, 
            :duration_minutes, :service_notes, :billing_status
        )";
        
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':client_id', $_POST['client_id'], PDO::PARAM_INT);
        $stmt->bindParam(':staff_id', $_POST['staff_id'], PDO::PARAM_INT);
        $stmt->bindParam(':service_date', $_POST['service_date']);
        $stmt->bindParam(':ndis_line_item', $_POST['ndis_line_item']);
        $stmt->bindParam(':duration_minutes', $_POST['duration_minutes'], PDO::PARAM_INT);
        $stmt->bindParam(':service_notes', $_POST['service_notes']);
        $stmt->bindParam(':billing_status', $_POST['billing_status']);
        
        $stmt->execute();
        
        $message = "Booking successfully added!";
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<header>
    <h1>Log a New Booking</h1>
</header>

<?php if ($message): ?>
<div class="feedback success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="feedback error"><?php echo $error; ?></div>
<?php endif; ?>

<form action="log_booking.php" method="POST">
    <div class="form-grid">
        <div class="form-group">
            <label for="client_id">Client *</label>
            <select id="client_id" name="client_id" required>
                <option value="">Select a client...</option>
                <?php foreach ($clients as $p): ?>
                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['full_legal_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
         <div class="form-group">
            <label for="staff_id">Care Staff *</label>
            <select id="staff_id" name="staff_id" required>
                <option value="">Select a staff member...</option>
                <?php foreach ($care_staff as $w): ?>
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
        <button type="submit" class="btn btn-primary">Log Booking</button>
        <a href="bookings.php" class="btn">Cancel</a>
    </div>
</form>

<?php require_once 'footer.php'; ?>