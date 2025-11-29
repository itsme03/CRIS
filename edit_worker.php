<?php
require_once 'db/config.php';
require_once 'header.php';

$message = '';
$error = '';
$worker = null;

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    echo '<div class="feedback error">No worker ID specified.</div>';
    require_once 'footer.php';
    exit;
}

$worker_id = $_GET['id'] ?? $_POST['id'];

try {
    $db = db();
    $stmt = $db->prepare("SELECT * FROM support_workers WHERE id = :id");
    $stmt->bindParam(':id', $worker_id, PDO::PARAM_INT);
    $stmt->execute();
    $worker = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$worker) {
        throw new Exception("Support worker not found.");
    }
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
    echo "<div class='feedback error'>$error</div>";
    require_once 'footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = db();
        
        if (empty($_POST['full_name']) || empty($_POST['id'])) {
            throw new Exception("Full Name and ID are required fields.");
        }

        $sql = "UPDATE support_workers SET 
            full_name = :full_name, 
            contact_info = :contact_info, 
            ndis_worker_screening_number = :ndis_worker_screening_number, 
            ndis_worker_screening_expiry = :ndis_worker_screening_expiry,
            first_aid_expiry = :first_aid_expiry, 
            qualifications = :qualifications, 
            hourly_rate = :hourly_rate
        WHERE id = :id";
        
        $stmt = $db->prepare($sql);

        $worker_id = $_POST['id'];

        $stmt->bindParam(':id', $worker_id, PDO::PARAM_INT);
        $stmt->bindParam(':full_name', $_POST['full_name']);
        $stmt->bindParam(':contact_info', $_POST['contact_info']);
        $stmt->bindParam(':ndis_worker_screening_number', $_POST['ndis_worker_screening_number']);
        $stmt->bindParam(':ndis_worker_screening_expiry', $_POST['ndis_worker_screening_expiry']);
        $stmt->bindParam(':first_aid_expiry', $_POST['first_aid_expiry']);
        $stmt->bindParam(':qualifications', $_POST['qualifications']);
        $stmt->bindParam(':hourly_rate', $_POST['hourly_rate']);
        
        $stmt->execute();
        
        header("Location: worker_detail.php?id=" . $worker_id . "&message=updated");
        exit;
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<header>
    <h1>Edit Support Worker: <?php echo htmlspecialchars($worker['full_name']); ?></h1>
</header>

<?php if ($message): ?>
<div class="feedback success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="feedback error"><?php echo $error; ?></div>
<?php endif; ?>

<form action="edit_worker.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $worker['id']; ">
    <div class="form-grid" style="grid-template-columns: 1fr;">
        <div class="form-group">
            <label for="full_name">Full Name *</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($worker['full_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="contact_info">Contact Info (Phone/Email)</label>
            <textarea id="contact_info" name="contact_info" rows="2"><?php echo htmlspecialchars($worker['contact_info']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="qualifications">Qualifications/Certificates</label>
            <textarea id="qualifications" name="qualifications" rows="3"><?php echo htmlspecialchars($worker['qualifications']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="ndis_worker_screening_number">NDIS Worker Screening Check Number</label>
            <input type="text" id="ndis_worker_screening_number" name="ndis_worker_screening_number" value="<?php echo htmlspecialchars($worker['ndis_worker_screening_number']); ?>">
        </div>
        <div class="form-group">
            <label for="ndis_worker_screening_expiry">Screening Check Expiry Date</label>
            <input type="date" id="ndis_worker_screening_expiry" name="ndis_worker_screening_expiry" value="<?php echo htmlspecialchars($worker['ndis_worker_screening_expiry']); ?>">
        </div>
        <div class="form-group">
            <label for="first_aid_expiry">First Aid Certificate Expiry Date</label>
            <input type="date" id="first_aid_expiry" name="first_aid_expiry" value="<?php echo htmlspecialchars($worker['first_aid_expiry']); ?>">
        </div>
        <div class="form-group">
            <label for="hourly_rate">Hourly Rate</label>
            <input type="number" step="0.01" id="hourly_rate" name="hourly_rate" value="<?php echo htmlspecialchars($worker['hourly_rate']); ?>">
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="worker_detail.php?id=<?php echo $worker['id']; ?>" class="btn">Cancel</a>
    </div>
</form>

<?php require_once 'footer.php'; ?>