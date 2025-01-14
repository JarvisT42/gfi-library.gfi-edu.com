<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Most Borrowed Books</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 80%;
            margin: auto;
        }
    </style>
</head>
<body>

<div class="chart-container">
    <canvas id="borrowedBooksChart"></canvas>
</div>

<script>
    // Data for the chart
    const labels = ['ID 001', 'ID 002', 'ID 003', 'ID 004', 'ID 005'];
    const data = {
        labels: labels,
        datasets: [{
            label: 'Number of Times Borrowed',
            data: [30, 50, 70, 40, 60], // Replace with actual borrow counts
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    };

    // Configuring the chart
    const config = {
        type: 'bar',
        data: data,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    // Rendering the chart
    const borrowedBooksChart = new Chart(
        document.getElementById('borrowedBooksChart'),
        config
    );
</script>

</body>
</html>
