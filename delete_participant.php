<?php
require_once 'db/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: participants.php');
    exit;
}

if (!isset($_POST['id']) || !filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
    header('Location: participants.php?error=invalid_id');
    exit;
}

$participant_id = $_POST['id'];

try {
    $db = db();
    $stmt = $db->prepare("DELETE FROM participants WHERE id = :id");
    $stmt->bindParam(':id', $participant_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        header('Location: participants.php?message=deleted');
    } else {
        header('Location: participants.php?error=not_found');
    }
    exit;

} catch (PDOException $e) {
    // In a real app, you'd log this error, not expose it
    header('Location: participants.php?error=db_error');
    exit;
}
