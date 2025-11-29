<?php
require_once 'db/config.php';
require_once 'header.php';

// Fetch counts for dashboard cards
$participant_count = db()->query("SELECT COUNT(*) FROM participants")->fetchColumn();
$worker_count = db()->query("SELECT COUNT(*) FROM support_workers")->fetchColumn();
$service_log_count = db()->query("SELECT COUNT(*) FROM service_logs")->fetchColumn();

// Fetch upcoming compliance alerts (e.g., expiring in 90 days)
$ninety_days_from_now = date('Y-m-d', strtotime('+90 days'));
$compliance_alerts = db()->query("SELECT COUNT(*) FROM participants WHERE ndis_plan_end_date <= '$ninety_days_from_now'")->fetchColumn();
$worker_alerts = db()->query("SELECT COUNT(*) FROM support_workers WHERE ndis_worker_screening_expiry <= '$ninety_days_from_now' OR first_aid_expiry <= '$ninety_days_from_now'")->fetchColumn();
$total_alerts = $compliance_alerts + $worker_alerts;

// Chart Data
// Participants per month
$participants_per_month_q = db()->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count FROM participants GROUP BY month ORDER BY month");
$participants_per_month = $participants_per_month_q->fetchAll(PDO::FETCH_ASSOC);
$participant_months = json_encode(array_column($participants_per_month, 'month'));
$participant_counts = json_encode(array_column($participants_per_month, 'count'));

// Services per month
$services_per_month_q = db()->query("SELECT DATE_FORMAT(service_date, '%Y-%m') as month, COUNT(*) as count FROM service_logs GROUP BY month ORDER BY month");
$services_per_month = $services_per_month_q->fetchAll(PDO::FETCH_ASSOC);
$service_months = json_encode(array_column($services_per_month, 'month'));
$service_counts = json_encode(array_column($services_per_month, 'count'));

// Billing status
$billing_status_q = db()->query("SELECT billing_status, COUNT(*) as count FROM service_logs GROUP BY billing_status");
$billing_status = $billing_status_q->fetchAll(PDO::FETCH_ASSOC);
$billing_status_labels = json_encode(array_column($billing_status, 'billing_status'));
$billing_status_counts = json_encode(array_column($billing_status, 'count'));

?>

<header>
    <h1>Dashboard</h1>
    <a href="log_service.php" class="btn btn-primary">Log a New Service</a>
</header>

<div class="card-container">
    <div class="card">
        <h3>Total Participants</h3>
        <p><?php echo $participant_count; ?></p>
        <a href="participants.php" class="card-link">Manage Participants &rarr;</a>
    </div>
    <div class="card">
        <h3>Total Support Workers</h3>
        <p><?php echo $worker_count; ?></p>
        <a href="workers.php" class="card-link">Manage Workers &rarr;</a>
    </div>
    <div class="card">
        <h3>Services Logged</h3>
        <p><?php echo $service_log_count; ?></p>
        <a href="service_logs.php" class="card-link">View Service Logs &rarr;</a>
    </div>
    <div class="card">
        <h3>Compliance Alerts</h3>
        <p><?php echo $total_alerts; ?></p>
        <a href="compliance.php" class="card-link">View Alerts &rarr;</a>
    </div>
</div>

<div class="charts-container">
    <div class="chart-card">
        <h3>Participants Added Per Month</h3>
        <canvas id="participantsChart"></canvas>
    </div>
    <div class="chart-card">
        <h3>Services Provided Per Month</h3>
        <canvas id="servicesChart"></canvas>
    </div>
    <div class="chart-card">
        <h3>Service Billing Status</h3>
        <canvas id="billingChart"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Participants Chart
        var participantsCtx = document.getElementById('participantsChart').getContext('2d');
        new Chart(participantsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $participant_months; ?>,
                datasets: [{
                    label: 'New Participants',
                    data: <?php echo $participant_counts; ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            }
        });

        // Services Chart
        var servicesCtx = document.getElementById('servicesChart').getContext('2d');
        new Chart(servicesCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $service_months; ?>,
                datasets: [{
                    label: 'Services Logged',
                    data: <?php echo $service_counts; ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            }
        });

        // Billing Status Chart
        var billingCtx = document.getElementById('billingChart').getContext('2d');
        new Chart(billingCtx, {
            type: 'pie',
            data: {
                labels: <?php echo $billing_status_labels; ?>,
                datasets: [{
                    label: 'Billing Status',
                    data: <?php echo $billing_status_counts; ?>,
                    backgroundColor: [
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });
    });
</script>

<?php require_once 'footer.php'; ?>