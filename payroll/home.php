<?php
include 'db_connect.php'; // Assuming this file includes your database connection

// Query to get attendance count per month
$attendance_query = "SELECT MONTH(datetime_log) AS month, COUNT(*) AS count FROM attendance GROUP BY MONTH(datetime_log)";
$attendance_result = mysqli_query($conn, $attendance_query);

// Query to get payroll count per month
$payroll_query = "SELECT MONTH(date_created) AS month, COUNT(*) AS count FROM payroll GROUP BY MONTH(date_created)";
$payroll_result = mysqli_query($conn, $payroll_query);

// Process the results into arrays for chart data and labels
$attendance_data = [];
$attendance_labels = [];
$payroll_data = [];
$payroll_labels = [];

while ($row = mysqli_fetch_assoc($attendance_result)) {
    $attendance_labels[] = date("F", mktime(0, 0, 0, $row['month'], 1)); // Convert month number to month name
    $attendance_data[] = $row['count'];
}

while ($row = mysqli_fetch_assoc($payroll_result)) {
    $payroll_labels[] = date("F", mktime(0, 0, 0, $row['month'], 1)); // Convert month number to month name
    $payroll_data[] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Data Graphs</title>
    <!-- Include Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row mt-3 ml-3">
            <div class="col-lg-6">
                <canvas id="attendanceChart" width="400" height="400"></canvas>
            </div>
            <div class="col-lg-6">
                <canvas id="payrollChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>

	<script>
    // Attendance Chart
    var attendanceData = <?php echo json_encode(array_values($attendance_data)); ?>;
    var attendanceChartLabels = <?php echo json_encode($attendance_labels); ?>;
    var attendanceChartCtx = document.getElementById('attendanceChart').getContext('2d');
    var attendanceChart = new Chart(attendanceChartCtx, {
        type: 'bar',
        data: {
            labels: attendanceChartLabels,
            datasets: [{
                label: 'Attendance Count',
                data: attendanceData,
                backgroundColor: 'rgba(255, 99, 132, 1)', // Solid color
                borderColor: 'rgba(0, 0, 0, 1)', // Black border color
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: 'black' // Y-axis tick color
                    }
                },
                x: {
                    ticks: {
                        color: 'black' // X-axis tick color
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: 'black' // Legend label color
                    }
                }
            }
        }
    });

    // Payroll Chart
    var payrollData = <?php echo json_encode(array_values($payroll_data)); ?>;
    var payrollChartLabels = <?php echo json_encode($payroll_labels); ?>;
    var payrollChartCtx = document.getElementById('payrollChart').getContext('2d');
    var payrollChart = new Chart(payrollChartCtx, {
        type: 'bar',
        data: {
            labels: payrollChartLabels,
            datasets: [{
                label: 'Payroll Count',
                data: payrollData,
                backgroundColor: 'rgba(75, 192, 192, 1)', // Solid color
                borderColor: 'rgba(0, 0, 0, 1)', // Black border color
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: 'black' // Y-axis tick color
                    }
                },
                x: {
                    ticks: {
                        color: 'black' // X-axis tick color
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: 'black' // Legend label color
                    }
                }
            }
        }
    });
</script>

</body>
</html>
