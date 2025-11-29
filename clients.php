<?php
require_once 'db/config.php';
require_once 'header.php';

$logs = [];
try {
    $db = db();
    $stmt = $db->query("SELECT id, full_legal_name, ndis_client_number, primary_phone, email FROM clients ORDER BY created_at DESC");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="feedback error">Error: ' . $e->getMessage() . '</div>';
}

$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';

?>

<header>
    <h1>Clients</h1>
    <a href="add_client.php" class="btn btn-primary">Add New Client</a>
</header>

<?php if ($message === 'deleted'): ?>
<div class="feedback success">Client successfully deleted.</div>
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
        <?php if (empty($clients)): ?>
            <tr>
                <td colspan="5" style="text-align: center;">No clients found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($clients as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['full_legal_name']); ?></td>
                    <td><?php echo htmlspecialchars($p['ndis_client_number']); ?></td>
                    <td><?php echo htmlspecialchars($p['primary_phone']); ?></td>
                    <td><?php echo htmlspecialchars($p['email']); ?></td>
                    <td>
                        <a href="client_detail.php?id=<?php echo $p['id']; ?>" class="btn">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>