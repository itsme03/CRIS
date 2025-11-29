<?php
require_once 'db/config.php';
require_once 'header.php';

// Fetch counts for dashboard cards
$client_count = db()->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$staff_count = db()->query("SELECT COUNT(*) FROM care_staff")->fetchColumn();
$booking_count = db()->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

// Fetch upcoming compliance alerts (e.g., expiring in 90 days)
$ninety_days_from_now = date('Y-m-d', strtotime('+90 days'));
$compliance_alerts = db()->query("SELECT COUNT(*) FROM clients WHERE ndis_plan_end_date <= '$ninety_days_from_now'")->fetchColumn();
$worker_alerts = db()->query("SELECT COUNT(*) FROM care_staff WHERE ndis_worker_screening_expiry <= '$ninety_days_from_now' OR first_aid_expiry <= '$ninety_days_from_now'")->fetchColumn();
$total_alerts = $compliance_alerts + $worker_alerts;

// Chart Data
// Clients per month
$clients_per_month_q = db()->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count FROM clients GROUP BY month ORDER BY month");
$clients_per_month = $clients_per_month_q->fetchAll(PDO::FETCH_ASSOC);
$client_months = json_encode(array_column($clients_per_month, 'month'));
$client_counts = json_encode(array_column($clients_per_month, 'count'));

// Bookings per month
$bookings_per_month_q = db()->query("SELECT DATE_FORMAT(service_date, '%Y-%m') as month, COUNT(*) as count FROM bookings GROUP BY month ORDER BY month");
$bookings_per_month = $bookings_per_month_q->fetchAll(PDO::FETCH_ASSOC);
$booking_months = json_encode(array_column($bookings_per_month, 'month'));
$booking_counts = json_encode(array_column($bookings_per_month, 'count'));

// Billing status
$billing_status_q = db()->query("SELECT billing_status, COUNT(*) as count FROM bookings GROUP BY billing_status");
$billing_status = $billing_status_q->fetchAll(PDO::FETCH_ASSOC);
$billing_status_labels = json_encode(array_column($billing_status, 'billing_status'));
$billing_status_counts = json_encode(array_column($billing_status, 'count'));

?>

<header>
    <h1>Dashboard</h1>
    <a href="log_booking.php" class="btn btn-primary">Log a New Booking</a>
</header>

<div class="card-container">
    <div class="card">
        <h3>Total Clients</h3>
        <p><?php echo $client_count; ?></p>
        <a href="clients.php" class="card-link">Manage Clients &rarr;</a>
    </div>
    <div class="card">
        <h3>Total Care Staff</h3>
        <p><?php echo $staff_count; ?></p>
        <a href="care_staff.php" class="card-link">Manage Care Staff &rarr;</a>
    </div>
    <div class="card">
        <h3>Bookings Logged</h3>
        <p><?php echo $booking_count; ?></p>
        <a href="bookings.php" class="card-link">View Bookings &rarr;</a>
    </div>
    <div class="card">
        <h3>Compliance Alerts</h3>
        <p><?php echo $total_alerts; ?></p>
        <a href="compliance.php" class="card-link">View Alerts &rarr;</a>
    </div>
</div>

<div class="charts-container">
    <div class="chart-card">
        <h3>Clients Added Per Month</h3>
        <canvas id="clientsChart"></canvas>
    </div>
    <div class="chart-card">
        <h3>Bookings Per Month</h3>
        <canvas id="bookingsChart"></canvas>
    </div>
    <div class="chart-card">
        <h3>Booking Billing Status</h3>
        <canvas id="billingChart"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Clients Chart
        var clientsCtx = document.getElementById('clientsChart').getContext('2d');
        new Chart(clientsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $client_months; ?>,
                datasets: [{
                    label: 'New Clients',
                    data: <?php echo $client_counts; ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            }
        });

        // Bookings Chart
        var bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
        new Chart(bookingsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $booking_months; ?>,
                datasets: [{
                    label: 'Bookings Logged',
                    data: <?php echo $booking_counts; ?>,
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