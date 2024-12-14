<?php
# Initialize the session
session_start();
require '../connection.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');

    exit;
}


$id = $_SESSION["Faculty_Id"];

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'user_header.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <style>
        /* If you prefer inline styles, you can include them directly */
        .active-dashboard {
            background-color: #f0f0f0;
            /* Example for light mode */
            color: #000;
            /* Example for light mode */
        }
    </style>
    <style>
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 100%;
            width: 100%;

        }

        h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #333;
        }

        canvas {
            margin-top: 20px;
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <?php include './src/components/sidebar.php'; ?>

    <main id="content" class="">


        <div class="p-4 sm:ml-64">

            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">

                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-muted-foreground">TOTAL PENDING BOOKS TO CLAIM</p>
                                <div class="flex items-center text-red-600">

                                </div>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <?php

                                $id = $_SESSION["Faculty_Id"];
                                // Prepared statement to prevent SQL injection


                                // Query to count borrowed books
                                $sql = "SELECT COUNT(*) AS total_borrowed FROM borrow WHERE status = 'pending' and student_id = $id"; // Replace 'books' with your table name and 'status' with your field
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // Output the count
                                    $row = $result->fetch_assoc();
                                    $total =  $row['total_borrowed'];
                                }

                                ?>

                                <h3 class="text-2xl font-bold"><?php echo $total; ?></h3> <!-- Example number for registered students -->
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <a href="#" class="text-sm font-medium text-primary hover:underline">View pending requests</a>
                                <div class="bg-green-400 h-12 w-12 flex items-center justify-center rounded-full"> <!-- Circle background with fixed width and height -->
                                    <i class="fas fa-book  text-white"></i> <!-- Icon size -->
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-muted-foreground">TOTAL PENDING BOOKS TO CLAIM</p>
                                <div class="flex items-center text-red-600">
                                </div>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <?php
                                // Prepared statement to prevent SQL injection
                                // Query to count borrowed books
                                $sql = "SELECT COUNT(*) AS total_borrowed FROM borrow WHERE status = 'ready_to_claim' and student_id = $id"; // Replace 'books' with your table name and 'status' with your field
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // Output the count
                                    $row = $result->fetch_assoc();
                                    $total =  $row['total_borrowed'];
                                }
                                ?>
                                <h3 class="text-2xl font-bold"><?php echo $total; ?></h3> <!-- Example number for registered students -->
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <a href="activity_log.php" class="text-sm font-medium text-primary hover:underline">View pending requests</a>
                                <div class="bg-green-400 h-12 w-12 flex items-center justify-center rounded-full"> <!-- Circle background with fixed width and height -->
                                    <i class="fas fa-book  text-white"></i> <!-- Icon size -->
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-muted-foreground">TOTAL BORROWED BOOKS</p>
                                <div class="flex items-center text-red-600">

                                </div>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <?php

                                // Prepared statement to prevent SQL injection


                                // Query to count borrowed books
                                $sql = "SELECT COUNT(*) AS total_borrowed FROM borrow WHERE status = 'borrowed' and student_id = $id"; // Replace 'books' with your table name and 'status' with your field
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // Output the count
                                    $row = $result->fetch_assoc();
                                    $total =  $row['total_borrowed'];
                                }

                                ?>

                                <h3 class="text-2xl font-bold"><?php echo $total; ?></h3> <!-- Example number for registered students -->
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <a href="#" class="text-sm font-medium text-primary hover:underline">View borrowed requests</a>
                                <div class="bg-green-400 h-12 w-12 flex items-center justify-center rounded-full"> <!-- Circle background with fixed width and height -->
                                    <i class="fas fa-book  text-white"></i> <!-- Icon size -->
                                </div>
                            </div>
                        </div>
                    </div>









                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-muted-foreground">DUE SOON</p>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <?php
                                // Start session and retrieve the student id from the session

                                // Prepared statement to retrieve the Issued_Date and Due_Date of borrowed books
                                $stmt = $conn->prepare("SELECT Issued_Date, Due_Date FROM borrow WHERE status = 'borrowed' AND student_id = ?");
                                $stmt->bind_param("i", $id); // Bind the student id to the query
                                $stmt->execute(); // Execute the prepared statement
                                $result = $stmt->get_result(); // Get the result of the query

                                // Initialize a variable to store the earliest due date
                                $due_date = null;

                                // Check if there are results
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $issued_date = $row['Issued_Date']; // Get the issued date
                                        $db_due_date = $row['Due_Date']; // Get the due date from the database

                                        // Check if Due_Date is empty, calculate based on Issued_Date + 3 days
                                        if (empty($db_due_date)) {
                                            $calculated_due_date = date('Y-m-d', strtotime($issued_date . ' + 3 days'));
                                        } else {
                                            $calculated_due_date = $db_due_date;
                                        }

                                        // Find the earliest due date
                                        if (is_null($due_date) || $calculated_due_date < $due_date) {
                                            $due_date = $calculated_due_date; // Set or update to the earliest date
                                        }
                                    }
                                } else {
                                    $due_date = 'No books borrowed'; // If no results, set fallback message
                                }

                                // Close the prepared statement
                                $stmt->close();
                                ?>

                                <!-- Display the earliest Due_Date -->
                                <h3 class="text-2xl font-bold"><?php echo $due_date; ?></h3>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <a href="#" class="text-sm font-medium text-primary hover:underline">View details</a>
                                <div class="bg-yellow-400 h-12 w-12 flex items-center justify-center rounded-full"> <!-- Circle background with fixed width and height -->
                                    <i class="fas fa-user-graduate text-white text-xl"></i> <!-- Icon for students -->
                                </div>
                            </div>
                        </div>
                    </div>







                </div>



                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">


                    <?php
                    // Step 1: Connect to the first database (GFI_Library_Database)
                    $host = "localhost";
                    $dbname1 = "dnllaaww_gfi_library";
                    $username = "dnllaaww_ramoza";
                    $password = "Ramoza@30214087695";

                    try {
                        $pdo1 = new PDO("mysql:host=$host;dbname=$dbname1", $username, $password);
                        $pdo1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    } catch (PDOException $e) {
                        die("Connection failed: " . $e->getMessage());
                    }

                   // Second database connection
                   $host2 = "localhost";
                   $dbname2 = "dnllaaww_gfi_library_books_inventory";
                   $username2 = "dnllaaww_ramoza";
                   $password2 = "Ramoza@30214087695";
                   
                   try {
                       $pdo2 = new PDO("mysql:host=$host2;dbname=$dbname2", $username2, $password2);
                       $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                   } catch (PDOException $e) {
                       die("Connection to second database failed: " . $e->getMessage());
                   }

                   

                    // Step 3: Get the current year
                    $currentYear = date("Y");  // e.g., 2024

                    // Step 4: Fetch the most borrowed book IDs and their categories for each month
                    $query = "
    SELECT 
        MONTH(date) AS month,
        book_id,
        COUNT(book_id) AS borrow_count,
        category
    FROM most_borrowed_books
    WHERE YEAR(date) = :currentYear
    GROUP BY month, book_id, category
    ORDER BY month ASC, borrow_count DESC
";

                    $stmt = $pdo1->prepare($query);
                    $stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
                    $stmt->execute();
                    $borrowedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Initialize an array to store the most borrowed book ID and titles per month
                    $borrowData = array_fill(1, 12, ['quantity' => 0, 'titles' => []]); // Initialize for 12 months

                    // Loop through each record and select the most borrowed book per month
                    foreach ($borrowedBooks as $row) {
                        $month = $row['month']; // Get the month number
                        $book_id = $row['book_id']; // Get the book ID
                        $category = $row['category']; // The table name
                        $borrowCount = $row['borrow_count']; // Get the borrow count

                        // Only store the most borrowed book for each month
                        if ($borrowData[$month]['quantity'] === 0 || $borrowCount > $borrowData[$month]['quantity']) {
                            // Query to get the book title from the corresponding category table
                            $titleQuery = "SELECT title FROM dnllaaww_gfi_library_books_inventory.`$category` WHERE id = :book_id";
                            $stmt2 = $pdo2->prepare($titleQuery);
                            $stmt2->bindParam(':book_id', $book_id, PDO::PARAM_INT);
                            $stmt2->execute();
                            $bookTitle = $stmt2->fetchColumn();

                            // Store the title and the count for that month
                            $borrowData[$month]['quantity'] = $borrowCount;  // Store the highest borrow count for the month
                            $borrowData[$month]['titles'] = [$bookTitle];    // Store the most borrowed book title for that month
                        }
                    }

                    // Convert the borrowData array into JSON for JavaScript
                    $jsonData = json_encode($borrowData);
                    ?>

                    <div class="md:col-span-2 rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <div class="container">
                                <h2>Most Borrowed Books Per Month (<?php echo $currentYear; ?>)</h2>
                                <canvas id="myChart"></canvas>
                            </div>

                            <script>
                                // Step 5: Pass the PHP data to JavaScript
                                let chartData = <?php echo $jsonData; ?>; // Get the monthly borrow data with titles

                                // Function to wrap long text into multiple lines
                                function wrapText(text, maxLineLength) {
                                    const words = text.split(' ');
                                    let lines = [];
                                    let currentLine = words[0];

                                    for (let i = 1; i < words.length; i++) {
                                        if (currentLine.length + words[i].length + 1 <= maxLineLength) {
                                            currentLine += ' ' + words[i];
                                        } else {
                                            lines.push(currentLine);
                                            currentLine = words[i];
                                        }
                                    }
                                    lines.push(currentLine); // Add the last line
                                    return lines;
                                }

                                // Prepare data for Chart.js
                                const labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                                const dataset = {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Borrowings',
                                        data: [],
                                        backgroundColor: [
                                            'rgba(255, 99, 132, 0.2)', // Colors for each bar
                                            'rgba(54, 162, 235, 0.2)',
                                            'rgba(255, 206, 86, 0.2)',
                                            'rgba(75, 192, 192, 0.2)',
                                            'rgba(153, 102, 255, 0.2)',
                                            'rgba(255, 159, 64, 0.2)',
                                            'rgba(199, 199, 199, 0.2)',
                                            'rgba(83, 102, 255, 0.2)',
                                            'rgba(170, 102, 255, 0.2)',
                                            'rgba(255, 202, 86, 0.2)',
                                            'rgba(99, 132, 255, 0.2)',
                                            'rgba(54, 235, 162, 0.2)'
                                        ],
                                        borderColor: [
                                            'rgba(255, 99, 132, 1)',
                                            'rgba(54, 162, 235, 1)',
                                            'rgba(255, 206, 86, 1)',
                                            'rgba(75, 192, 192, 1)',
                                            'rgba(153, 102, 255, 1)',
                                            'rgba(255, 159, 64, 1)',
                                            'rgba(199, 199, 199, 1)',
                                            'rgba(83, 102, 255, 1)',
                                            'rgba(170, 102, 255, 1)',
                                            'rgba(255, 202, 86, 1)',
                                            'rgba(99, 132, 255, 1)',
                                            'rgba(54, 235, 162, 1)'
                                        ],
                                        borderWidth: 1
                                    }]
                                };

                                // Populate the data from the PHP results
                                labels.forEach((month, index) => {
                                    const monthData = chartData[index + 1]; // Month numbers in PHP are 1-indexed (January is 1, not 0)
                                    if (monthData) {
                                        // Push the borrow count for each month
                                        dataset.datasets[0].data.push(monthData.quantity);
                                    } else {
                                        dataset.datasets[0].data.push(0); // If no data for the month, push 0
                                    }
                                });

                                const ctx = document.getElementById('myChart').getContext('2d');
                                const myChart = new Chart(ctx, {
                                    type: 'bar',
                                    data: dataset,
                                    options: {
                                        responsive: true,
                                        scales: {
                                            y: {
                                                beginAtZero: true, // Start at zero
                                                title: {
                                                    display: true,
                                                    text: 'Times Borrowed'
                                                },
                                                ticks: {
                                                    stepSize: 1, // Set the interval for each step to 1
                                                    callback: function(value) { // Format the labels as integers
                                                        if (value % 1 === 0) {
                                                            return value;
                                                        }
                                                    }
                                                }
                                            },
                                            x: {
                                                title: {
                                                    display: true,
                                                    text: 'Months'
                                                }
                                            }
                                        },
                                        plugins: {
                                            legend: {
                                                display: false,
                                            },
                                            title: {
                                                display: true,
                                                text: 'Most Borrowed Books per Month for <?php echo $currentYear; ?>'
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(tooltipItem) {
                                                        const monthIndex = tooltipItem.dataIndex + 1; // Get the 1-indexed month
                                                        const monthData = chartData[monthIndex]; // Get data for that month
                                                        if (monthData && monthData.titles.length > 0) {
                                                            const maxLineLength = 30; // Max characters per line before wrapping
                                                            const wrappedTitles = monthData.titles.map(title => wrapText(title, maxLineLength));
                                                            const flatTitles = wrappedTitles.flat(); // Flatten array of arrays
                                                            return flatTitles.concat(`${tooltipItem.raw} times borrowed`);
                                                        } else {
                                                            return `No data for this month`;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            </script>
                        </div>
                    </div>








                    <div class="rounded-lg border bg-white text-gray-700 shadow-lg transition duration-300 hover:shadow-xl">
                        <div class="p-8">
                            <!-- Upcoming Dues -->


                            <div class="bg-white rounded-lg shadow-md p-6">
                                


                            <img src="../src/assets/images/library.png" alt="Your Image Description">



                            </div>



                            <!-- Today's Borrowing -->
                            <div class="bg-white rounded-lg shadow-md p-6 mt-8">
                              
                            <h1>LIBRARY</h1>
                            </div>


                        </div>
                    </div>

                    













                </div>






            </div>
        </div>

    </main>



    <script src="./src/components/header.js"></script>


</body>

</html>