<?php
# Initialize the session
require '../connection.php';

session_start();


if (!isset($_SESSION['logged_Admin']) || $_SESSION['logged_Admin'] !== true) {
    header('Location: ../index.php');

    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'admin_header.php'; ?>

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

                <div class="col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">



                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm ">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <p class="text-lg font-medium text-muted-foreground">TOTAL PENDING BORROW BOOKS</p>
                                <!-- <div class="flex items-center text-red-600">
                                    <i class="fas fa-arrow-down mr-1 h-4 w-4"></i>
                                    <span class="text-sm font-medium">5.12%</span>
                                </div> -->
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <?php


                                // Query to count borrowed books
                                $sql = "SELECT COUNT(*) AS total_borrowed FROM borrow WHERE status = 'pending'"; // Replace 'books' with your table name and 'status' with your field
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
                                <a href="book_request.php" class="text-sm font-medium text-primary hover:underline">View pending requests</a>
                                <div class="bg-green-400 h-12 w-12 flex items-center justify-center rounded-full"> <!-- Circle background with fixed width and height -->
                                    <i class="fas fa-book  text-white"></i> <!-- Icon size -->
                                </div>
                            </div>
                        </div>
                    </div>





                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <p class="text-lg font-medium text-muted-foreground">REGISTERED STUDENTS</p>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <?php

                                // Query to count all students
                                $sql = "SELECT COUNT(*) AS total_students FROM students"; // No WHERE clause, counting all rows
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // Output the count
                                    $row = $result->fetch_assoc();
                                    $total =  $row['total_students'];
                                } else {
                                    $total = 0; // Fallback in case no rows are found
                                }

                                ?>

                                <h3 class="text-2xl font-bold"><?php echo $total; ?></h3> <!-- Display total count of students -->
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <a href="students.php" class="text-sm font-medium text-primary hover:underline">View student details</a>
                                <div class="bg-yellow-400 h-12 w-12 flex items-center justify-center rounded-full"> <!-- Circle background with fixed width and height -->
                                    <i class="fas fa-user-graduate text-white text-xl"></i> <!-- Icon for students -->
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <p class="text-lg font-medium text-muted-foreground">BOOKS IN INVENTORY</p>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <?php
                                // Database connection
                                $conn2 = mysqli_connect("localhost", "dnllaaww_ramoza", "Ramoza@30214087695", "dnllaaww_gfi_library_books_inventory");

                                if (!$conn2) {
                                    die("Connection failed: " . mysqli_connect_error());
                                }

                                // Query to fetch all table names
                                $tablesResult = $conn2->query("SHOW TABLES");

                                $total = 0; // Initialize total count

                                // Loop through each table
                                if ($tablesResult->num_rows > 0) {
                                    while ($table = $tablesResult->fetch_array()) {
                                        $tableName = $table[0];
                                        // Count rows in the current table
                                        $countResult = $conn2->query("SELECT COUNT(*) AS total FROM `$tableName`");

                                        if ($countResult) {
                                            $countRow = $countResult->fetch_assoc();
                                            $total += $countRow['total']; // Sum the counts
                                        }
                                    }
                                }

                                ?>

                                <h3 class="text-2xl font-bold"><?php echo $total; ?></h3> <!-- Display total count of rows in all tables -->
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <a href="books.php" class="text-sm font-medium text-primary hover:underline">View inventory</a>
                                <div class="bg-blue-400 h-12 w-12 flex items-center justify-center rounded-full">
                                    <i class="fas fa-book-open text-white text-xl"></i> <!-- Icon for inventory -->
                                </div>
                            </div>
                        </div>
                    </div>




                </div>




                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Left Section (Chart) -->

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

                    // Step 2: Connect to the second database (gfi_library_database_books_records)
                    $dbname2 = "dnllaaww_gfi_library_books_inventory";

                    try {
                        $pdo2 = new PDO("mysql:host=$host;dbname=$dbname2", $username, $password);
                        $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    } catch (PDOException $e) {
                        die("Connection failed: " . $e->getMessage());
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













                    <!-- Right Section (Dues and Borrowing) -->
                    <div class="rounded-lg border bg-white text-gray-700 shadow-lg transition duration-300 hover:shadow-xl">
                        <div class="p-8">
                            <!-- Upcoming Dues -->


                            <div class="bg-white rounded-lg shadow-md p-6">
                                <?php
                                // Calculate today's date
                                $today = date('Y-m-d');

                                // Calculate a "Due Soon" date range (e.g., next 3 days)
                                $dueSoonStart = date('Y-m-d');
                                $dueSoonEnd = date('Y-m-d', strtotime('+3 days'));

                                // Due Today
                                $sql = "SELECT COUNT(*) as total_due_today FROM borrow WHERE status = 'pending' AND Return_Date = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('s', $today);
                                $stmt->execute();
                                $stmt->bind_result($total_due_today);
                                $stmt->fetch();
                                $stmt->close();

                                // Due Soon (within the next 3 days but not today)
                                $sql = "SELECT COUNT(*) as total_due_soon FROM borrow WHERE status = 'pending' AND Return_Date > ? AND Return_Date <= ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('ss', $dueSoonStart, $dueSoonEnd);
                                $stmt->execute();
                                $stmt->bind_result($total_due_soon);
                                $stmt->fetch();
                                $stmt->close();

                                // Due Later (beyond the next 3 days)
                                $sql = "SELECT COUNT(*) as total_due_later FROM borrow WHERE status = 'pending' AND Return_Date > ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('s', $dueSoonEnd);
                                $stmt->execute();
                                $stmt->bind_result($total_due_later);
                                $stmt->fetch();
                                $stmt->close();

                                // Calculate the total
                                $total_books_due = $total_due_today + $total_due_soon + $total_due_later;
                                ?>

                                <h2 class="font-semibold text-xl mb-6 flex items-center justify-between">
                                    Book Return Deadlines
                                    <span class="text-md font-normal text-gray-500">
                                        <?php echo $total_books_due; ?> Total
                                    </span>
                                </h2>

                                <div class="space-y-6">
                                    <!-- Due Soon -->
                                    <a href="borrowed_books.php" class="block">
    <div class="flex justify-between items-center hover:bg-gray-100 p-3 rounded-lg transition duration-200">
        <div class="flex items-start">
            <span class="bg-green-500 w-4 h-4 rounded-full mt-1.5 mr-3 flex-shrink-0"></span>
            <p class="text-lg font-medium">Due Soon</p>
        </div>
        <span class="text-md text-gray-500"><?php echo $total_due_soon; ?> Books Due</span>
    </div>
</a>


                                    <!-- Due Today -->
                                    <a href="borrowed_books.php" class="block">

                                    <div class="flex justify-between items-center hover:bg-gray-100 p-3 rounded-lg transition duration-200">
                                        <div class="flex items-start">
                                            <span class="bg-red-500 w-4 h-4 rounded-full mt-1.5 mr-3 flex-shrink-0"></span>
                                            <p class="text-lg font-medium">Due Today</p>
                                        </div>
                                        <span class="text-md text-gray-500"><?php echo $total_due_today; ?> Books Due</span>
                                    </div>
                                    </a>

                                    <!-- Due Later -->
                                    <a href="borrowed_books.php" class="block">

                                    <div class="flex justify-between items-center hover:bg-gray-100 p-3 rounded-lg transition duration-200">
                                        <div class="flex items-start">
                                            <span class="bg-blue-500 w-4 h-4 rounded-full mt-1.5 mr-3 flex-shrink-0"></span>
                                            <p class="text-lg font-medium">Due Later</p>
                                        </div>
                                        <span class="text-md text-gray-500"><?php echo $total_due_later; ?> Books Due</span>
                                    </div>
                                    </a>

                                </div>
                            </div>



                            <!-- Today's Borrowing -->
                            <div class="bg-white rounded-lg shadow-md p-6 mt-8">
                                <?php
                                // Calculate tomorrow's date
                                $tomorrow = date('Y-m-d', strtotime('+1 day'));

                                // Calculate today's date
                                $today = date('Y-m-d');

                                // Query to count how many books are scheduled to be borrowed tomorrow
                                $sql = "SELECT COUNT(*) as total_requests FROM borrow WHERE status = 'pending' AND Date_To_Claim = ? and Return_Date IS NULL";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('s', $tomorrow);
                                $stmt->execute();
                                $stmt->bind_result($total_requests_tomorrow);
                                $stmt->fetch();
                                $stmt->close();

                                // Query to count how many books are scheduled to be borrowed today
                                $sql = "SELECT COUNT(*) as total_requests FROM borrow WHERE status = 'pending' AND Date_To_Claim = ? and Return_Date IS NULL";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('s', $today);
                                $stmt->execute();
                                $stmt->bind_result($total_requests_today);
                                $stmt->fetch();
                                $stmt->close();

                                // Query to count how many books are scheduled to be borrowed later (after tomorrow)
                                $sql = "SELECT COUNT(*) as total_requests FROM borrow WHERE status = 'pending' AND Date_To_Claim > ? and Return_Date IS NULL";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('s', $tomorrow);
                                $stmt->execute();
                                $stmt->bind_result($total_requests_later);
                                $stmt->fetch();
                                $stmt->close();

                                // Calculate the total requests by summing today's, tomorrow's, and later requests
                                $total_requests = $total_requests_today + $total_requests_tomorrow + $total_requests_later;
                                ?>

                                <h2 class="font-semibold text-xl mb-6 flex items-center justify-between">
                                    Upcoming Borrowing Requests
                                    <span class="text-md font-normal text-gray-500">
                                        <?php echo $total_requests; ?> Total
                                    </span>
                                </h2>

                                <div class="space-y-6">
                                    <!-- To Borrow Tomorrow -->


                                    <a href="book_request.php" class="block">

                                    <div class="flex justify-between items-center hover:bg-gray-100 p-3 rounded-lg transition duration-200">
                                        <div class="flex items-start">
                                            <span class="bg-green-500 w-4 h-4 rounded-full mt-1.5 mr-3 flex-shrink-0"></span>
                                            <p class="text-lg font-medium">To Borrow Tomorrow</p>
                                        </div>
                                        <span class="text-md text-gray-500"><?php echo $total_requests_tomorrow; ?> Requests</span>
                                    </div>
                                    </a>

                                    <!-- To Borrow Today -->

                                    <a href="book_request.php" class="block">

                                    <div class="flex justify-between items-center hover:bg-gray-100 p-3 rounded-lg transition duration-200">
                                        <div class="flex items-start">
                                            <span class="bg-red-500 w-4 h-4 rounded-full mt-1.5 mr-3 flex-shrink-0"></span>
                                            <p class="text-lg font-medium">To Borrow Today</p>
                                        </div>
                                        <span class="text-md text-gray-500"><?php echo $total_requests_today; ?> Requests</span>
                                    </div>
                                    </a>

                                    <!-- To Borrow Later -->
                                    <a href="book_request.php" class="block">

                                    <div class="flex justify-between items-center hover:bg-gray-100 p-3 rounded-lg transition duration-200">
                                        <div class="flex items-start">
                                            <span class="bg-blue-500 w-4 h-4 rounded-full mt-1.5 mr-3 flex-shrink-0"></span>
                                            <p class="text-lg font-medium">To Borrow Later</p>
                                        </div>
                                        <span class="text-md text-gray-500"><?php echo $total_requests_later; ?> Requests</span>
                                    </div>
                                    </a>

                                </div>
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