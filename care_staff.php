<?php
require_once 'db/config.php';
require_once 'header.php';

$care_staff = [];
try {
    $db = db();
    $stmt = $db->query("SELECT id, full_name, ndis_worker_screening_number, first_aid_expiry FROM care_staff ORDER BY created_at DESC");
    $care_staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="feedback error">Error: ' . $e->getMessage() . '</div>';
}

?>

<header>
    <h1>Care Staff</h1>
    <a href="add_staff.php" class="btn btn-primary">Add New Staff Member</a>
</header>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Screening Number</th>
            <th>First Aid Expiry</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($care_staff)):
            <tr>
                <td colspan="4" style="text-align: center;">No care staff found.</td>
            </tr>
        <?php else:
            <?php foreach ($care_staff as $w):
                <tr>
                    <td><?php echo htmlspecialchars($w['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($w['ndis_worker_screening_number']); ?></td>
                    <td><?php echo htmlspecialchars(date("d M Y", strtotime($w['first_aid_expiry']))); ?></td>
                    <td>
                        <a href="staff_detail.php?id=<?php echo $w['id']; ?>" class="btn">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>