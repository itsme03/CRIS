<?php
require_once 'db/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: clients.php');
    exit;
}

if (!isset($_POST['id']) || !filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
    header('Location: clients.php?error=invalid_id');
    exit;
}

$client_id = $_POST['id'];

try {
    $db = db();
    $stmt = $db->prepare("DELETE FROM clients WHERE id = :id");
    $stmt->bindParam(':id', $client_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        header('Location: clients.php?message=deleted');
    } else {
        header('Location: clients.php?error=not_found');
    }
    exit;

} catch (PDOException $e) {
    // In a real app, you'd log this error, not expose it
    header('Location: clients.php?error=db_error');
    exit;
}
