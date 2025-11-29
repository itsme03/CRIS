<?php
require_once 'db/config.php';
require_once 'header.php';

$logs = [];
try {
    $db = db();
    $stmt = $db->query("SELECT id, full_legal_name, ndis_participant_number, primary_phone, email FROM participants ORDER BY created_at DESC");
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="feedback error">Error: ' . $e->getMessage() . '</div>';
}

$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';

?>

<header>
    <h1>Participants</h1>
    <a href="add_participant.php" class="btn btn-primary">Add New Participant</a>
</header>

<?php if ($message === 'deleted'): ?>
<div class="feedback success">Participant successfully deleted.</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="feedback error">An error occurred. <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>NDIS Number</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($participants)): ?>
            <tr>
                <td colspan="5" style="text-align: center;">No participants found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($participants as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['full_legal_name']); ?></td>
                    <td><?php echo htmlspecialchars($p['ndis_participant_number']); ?></td>
                    <td><?php echo htmlspecialchars($p['primary_phone']); ?></td>
                    <td><?php echo htmlspecialchars($p['email']); ?></td>
                    <td>
                        <a href="participant_detail.php?id=<?php echo $p['id']; ?>" class="btn">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>