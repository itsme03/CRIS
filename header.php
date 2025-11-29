<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NDIS Mini CRM</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/main.js" defer></script>
</head>
<body>

<?php
$current_page = basename($_SERVER['PHP_SELF']);
$pages = [
    'index.php' => 'Dashboard',
    'participants.php' => 'Participants',
    'workers.php' => 'Support Workers',
    'service_logs.php' => 'Service Logs',
    'compliance.php' => 'Compliance',
];
?>

<div class="sidebar">
    <h1 class="logo">NDIS CRM</h1>
    <nav>
        <?php foreach ($pages as $url => $title): ?>
            <a href="<?php echo $url; ?>" class="<?php echo ($current_page == $url) ? 'active' : ''; ?>">
                <?php echo $title; ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="footer">
        <p>&copy; <?php echo date("Y"); ?> NDIS CRM</p>
    </div>
</div>

<div class="main-content">
