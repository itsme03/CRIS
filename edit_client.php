<?php
require_once 'db/config.php';
require_once 'header.php';

$message = '';
$error = '';
$client = null;

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    echo '<div class="feedback error">No client ID specified.</div>';
    require_once 'footer.php';
    exit;
}

$client_id = $_GET['id'] ?? $_POST['id'];

try {
    $db = db();
    $stmt = $db->prepare("SELECT * FROM clients WHERE id = :id");
    $stmt->bindParam(':id', $client_id, PDO::PARAM_INT);
    $stmt->execute();
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        throw new Exception("Client not found.");
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
        
        $required_fields = ['full_legal_name', 'id'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("'$field' is a required field.");
            }
        }

        $sql = "UPDATE clients SET 
            full_legal_name = :full_legal_name, 
            ndis_client_number = :ndis_client_number, 
            date_of_birth = :date_of_birth, 
            preferred_contact_method = :preferred_contact_method,
            primary_phone = :primary_phone, 
            email = :email, 
            address = :address, 
            emergency_contact_name = :emergency_contact_name, 
            emergency_contact_phone = :emergency_contact_phone,
            ndis_plan_start_date = :ndis_plan_start_date, 
            ndis_plan_end_date = :ndis_plan_end_date, 
            plan_manager_name = :plan_manager_name, 
            plan_manager_contact = :plan_manager_contact,
            ndis_funding_budget_total = :ndis_funding_budget_total, 
            primary_disability = :primary_disability, 
            support_needs_summary = :support_needs_summary,
            communication_aids_methods = :communication_aids_methods, 
            behaviours_of_concern = :behaviours_of_concern, 
            risk_assessment_summary = :risk_assessment_summary,
            safety_plan = :safety_plan, 
            consent_for_info_sharing = :consent_for_info_sharing, 
            intake_notes = :intake_notes
        WHERE id = :id";
        
        $stmt = $db->prepare($sql);

        $consent = isset($_POST['consent_for_info_sharing']) ? 1 : 0;
        $client_id = $_POST['id'];

        $stmt->bindParam(':id', $client_id, PDO::PARAM_INT);
        $stmt->bindParam(':full_legal_name', $_POST['full_legal_name']);
        $stmt->bindParam(':ndis_client_number', $_POST['ndis_client_number']);
        $stmt->bindParam(':date_of_birth', $_POST['date_of_birth']);
        $stmt->bindParam(':preferred_contact_method', $_POST['preferred_contact_method']);
        $stmt->bindParam(':primary_phone', $_POST['primary_phone']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':address', $_POST['address']);
        $stmt->bindParam(':emergency_contact_name', $_POST['emergency_contact_name']);
        $stmt->bindParam(':emergency_contact_phone', $_POST['emergency_contact_phone']);
        $stmt->bindParam(':ndis_plan_start_date', $_POST['ndis_plan_start_date']);
        $stmt->bindParam(':ndis_plan_end_date', $_POST['ndis_plan_end_date']);
        $stmt->bindParam(':plan_manager_name', $_POST['plan_manager_name']);
        $stmt->bindParam(':plan_manager_contact', $_POST['plan_manager_contact']);
        $stmt->bindParam(':ndis_funding_budget_total', $_POST['ndis_funding_budget_total']);
        $stmt->bindParam(':primary_disability', $_POST['primary_disability']);
        $stmt->bindParam(':support_needs_summary', $_POST['support_needs_summary']);
        $stmt->bindParam(':communication_aids_methods', $_POST['communication_aids_methods']);
        $stmt->bindParam(':behaviours_of_concern', $_POST['behaviours_of_concern']);
        $stmt->bindParam(':risk_assessment_summary', $_POST['risk_assessment_summary']);
        $stmt->bindParam(':safety_plan', $_POST['safety_plan']);
        $stmt->bindParam(':consent_for_info_sharing', $consent, PDO::PARAM_INT);
        $stmt->bindParam(':intake_notes', $_POST['intake_notes']);
        
        $stmt->execute();
        
        header("Location: client_detail.php?id=" . $client_id . "&message=updated");
        exit;
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<style>
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
    .form-section { background-color: #fdfdfd; padding: 1.5rem; border-radius: 8px; border: 1px solid #eee; }
    .form-section h3 { font-size: 1.2rem; margin-bottom: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 1rem; }
    .ai-section { background-color: #eaf5ff; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #c7dfff; }
</style>

<header>
    <h1>Edit Client: <?php echo htmlspecialchars($client['full_legal_name']); ?></h1>
</header>

<?php if ($message): ?>
<div class="feedback success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="feedback error"><?php echo $error; ?></div>
<?php endif; ?>

<form action="edit_client.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $client['id']; ?>">

    <div class="ai-section">
        <h3>AI-Assisted Intake</h3>
        <div class="form-group">
            <label for="intake_notes">Raw Intake Notes</label>
            <textarea id="intake_notes" name="intake_notes" rows="6" placeholder="Paste the full, unstructured intake notes here..."><?php echo htmlspecialchars($client['intake_notes']); ?></textarea>
        </div>
        <button type="button" class="btn btn-secondary" id="summarize-with-ai">Summarize with AI</button>
    </div>

    <div class="form-grid">
        <div class="form-section">
            <h3>Client Details</h3>
            <div class="form-group">
                <label for="full_legal_name">Full Legal Name *</label>
                <input type="text" id="full_legal_name" name="full_legal_name" value="<?php echo htmlspecialchars($client['full_legal_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="ndis_client_number">NDIS Client Number</label>
                <input type="text" id="ndis_client_number" name="ndis_client_number" value="<?php echo htmlspecialchars($client['ndis_client_number']); ?>">
            </div>
            <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($client['date_of_birth']); ?>">
            </div>
             <div class="form-group">
                <label for="preferred_contact_method">Preferred Contact Method</label>
                <input type="text" id="preferred_contact_method" name="preferred_contact_method" value="<?php echo htmlspecialchars($client['preferred_contact_method']); ?>">
            </div>
        </div>

        <div class="form-section">
            <h3>Contact Info</h3>
            <div class="form-group">
                <label for="primary_phone">Primary Phone</label>
                <input type="tel" id="primary_phone" name="primary_phone" value="<?php echo htmlspecialchars($client['primary_phone']); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($client['email']); ?>">
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="1"><?php echo htmlspecialchars($client['address']); ?></textarea>
            </div>
             <div class="form-group">
                <label for="emergency_contact_name">Emergency Contact Name</label>
                <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="<?php echo htmlspecialchars($client['emergency_contact_name']); ?>">
            </div>
             <div class="form-group">
                <label for="emergency_contact_phone">Emergency Contact Phone</label>
                <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" value="<?php echo htmlspecialchars($client['emergency_contact_phone']); ?>">
            </div>
        </div>

        <div class="form-section">
            <h3>Plan Details</h3>
            <div class="form-group">
                <label for="ndis_plan_start_date">NDIS Plan Start Date</label>
                <input type="date" id="ndis_plan_start_date" name="ndis_plan_start_date" value="<?php echo htmlspecialchars($client['ndis_plan_start_date']); ?>">
            </div>
            <div class="form-group">
                <label for="ndis_plan_end_date">NDIS Plan End Date</label>
                <input type="date" id="ndis_plan_end_date" name="ndis_plan_end_date" value="<?php echo htmlspecialchars($client['ndis_plan_end_date']); ?>">
            </div>
            <div class="form-group">
                <label for="plan_manager_name">Plan Manager Name</label>
                <input type="text" id="plan_manager_name" name="plan_manager_name" value="<?php echo htmlspecialchars($client['plan_manager_name']); ?>">
            </div>
            <div class="form-group">
                <label for="plan_manager_contact">Plan Manager Contact</label>
                <input type="text" id="plan_manager_contact" name="plan_manager_contact" value="<?php echo htmlspecialchars($client['plan_manager_contact']); ?>">
            </div>
            <div class="form-group">
                <label for="ndis_funding_budget_total">NDIS Funding Budget (Total)</label>
                <input type="number" step="0.01" id="ndis_funding_budget_total" name="ndis_funding_budget_total" value="<?php echo htmlspecialchars($client['ndis_funding_budget_total']); ?>">
            </div>
        </div>

        <div class="form-section">
             <h3>Disability & Needs</h3>
            <div class="form-group">
                <label for="primary_disability">Primary Disability</label>
                <textarea id="primary_disability" name="primary_disability" rows="2"><?php echo htmlspecialchars($client['primary_disability']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="support_needs_summary">Support Needs Summary</label>
                <textarea id="support_needs_summary" name="support_needs_summary" rows="2"><?php echo htmlspecialchars($client['support_needs_summary']); ?></textarea>
            </div>
             <div class="form-group">
                <label for="communication_aids_methods">Communication Aids/Methods</label>
                <textarea id="communication_aids_methods" name="communication_aids_methods" rows="2"><?php echo htmlspecialchars($client['communication_aids_methods']); ?></textarea>
            </div>
        </div>

        <div class="form-section">
            <h3>Risk & Safety</h3>
             <div class="form-group">
                <label for="behaviours_of_concern">Known Behaviours of Concern</label>
                <textarea id="behaviours_of_concern" name="behaviours_of_concern" rows="3"><?php echo htmlspecialchars($client['behaviours_of_concern']); ?></textarea>
            </div>
             <div class="form-group">
                <label for="risk_assessment_summary">Detailed Risk Assessment Summary</label>
                <textarea id="risk_assessment_summary" name="risk_assessment_summary" rows="3"><?php echo htmlspecialchars($client['risk_assessment_summary']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="safety_plan">Safety/Restrictive Practices Plan</label>
                <textarea id="safety_plan" name="safety_plan" rows="3"><?php echo htmlspecialchars($client['safety_plan']); ?></textarea>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Consent</h3>
            <div class="form-group">
                <input type="checkbox" id="consent_for_info_sharing" name="consent_for_info_sharing" value="1" <?php echo $client['consent_for_info_sharing'] ? 'checked' : ''; ?> style="width: auto; margin-right: 10px;">
                <label for="consent_for_info_sharing">Consent for information sharing has been recorded.</label>
            </div>
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="client_detail.php?id=<?php echo $client['id']; ?>" class="btn">Cancel</a>
    </div>
</form>

<?php require_once 'footer.php'; ?>