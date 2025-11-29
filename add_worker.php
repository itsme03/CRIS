<?php
require_once 'db/config.php';
require_once 'header.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = db();
        
        if (empty($_POST['full_name'])) {
            throw new Exception("Full Name is a required field.");
        }

        $sql = "INSERT INTO support_workers (
            full_name, contact_info, ndis_worker_screening_number, 
            ndis_worker_screening_expiry, first_aid_expiry, qualifications, hourly_rate
        ) VALUES (
            :full_name, :contact_info, :ndis_worker_screening_number, 
            :ndis_worker_screening_expiry, :first_aid_expiry, :qualifications, :hourly_rate
        )";
        
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':full_name', $_POST['full_name']);
        $stmt->bindParam(':contact_info', $_POST['contact_info']);
        $stmt->bindParam(':ndis_worker_screening_number', $_POST['ndis_worker_screening_number']);
        $stmt->bindParam(':ndis_worker_screening_expiry', $_POST['ndis_worker_screening_expiry']);
        $stmt->bindParam(':first_aid_expiry', $_POST['first_aid_expiry']);
        $stmt->bindParam(':qualifications', $_POST['qualifications']);
        $stmt->bindParam(':hourly_rate', $_POST['hourly_rate']);
        
        $stmt->execute();
        
        $message = "Support worker successfully added!";
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<header>
    <h1>Add New Support Worker</h1>
</header>

<?php if ($message): ?>
<div class="feedback success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="feedback error"><?php echo $error; ?></div>
<?php endif; ?>

<form action="add_worker.php" method="POST">
    <div class="form-grid" style="grid-template-columns: 1fr;">
        <div class="form-group">
            <label for="full_name">Full Name *</label>
            <input type="text" id="full_name" name="full_name" required>
        </div>
        <div class="form-group">
            <label for="contact_info">Contact Info (Phone/Email)</label>
            <textarea id="contact_info" name="contact_info" rows="2"></textarea>
        </div>
        <div class="form-group">
            <label for="qualifications">Qualifications/Certificates</label>
            <textarea id="qualifications" name="qualifications" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label for="ndis_worker_screening_number">NDIS Worker Screening Check Number</label>
            <input type="text" id="ndis_worker_screening_number" name="ndis_worker_screening_number">
        </div>
        <div class="form-group">
            <label for="ndis_worker_screening_expiry">Screening Check Expiry Date</label>
            <input type="date" id="ndis_worker_screening_expiry" name="ndis_worker_screening_expiry">
        </div>
        <div class="form-group">
            <label for="first_aid_expiry">First Aid Certificate Expiry Date</label>
            <input type="date" id="first_aid_expiry" name="first_aid_expiry">
        </div>
        <div class="form-group">
            <label for="hourly_rate">Hourly Rate</label>
            <input type="number" step="0.01" id="hourly_rate" name="hourly_rate">
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <button type="submit" class="btn btn-primary">Add Worker</button>
        <a href="workers.php" class="btn">Cancel</a>
    </div>
</form>

<?php require_once 'footer.php'; ?>