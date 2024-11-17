<?php
# Initialize the session
session_start();
require '../connection.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
    header("Location: ../index.php");
    exit;
}

$Student_Id = $_SESSION["Student_Id"];

if (!isset($_SESSION['first_login'])) {
    $_SESSION['first_login'] = true;
}

// Check if it's the user's first login
$showModal = isset($_SESSION['first_login']) && $_SESSION['first_login'] === true;

// Reset the flag after showing the modal once
if ($showModal) {
    $_SESSION['first_login'] = false;
}

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
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">



                
<div id="donationModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Support Our Library Development Project</h2>
        <p>We are fourth-year students at Gensantos Foundation College, Inc., dedicated to creating a powerful and user-friendly Library Management System to benefit all students. Your support means a lot to us, as it helps us maintain, improve, and expand this project.</p>
        <p>If you'd like to support us, here are several ways to make a contribution:</p>

        <!-- E-Wallet Donation Options -->
        <div class="donation-options">
            <h3>E-Wallet Options:</h3>
            <p>Click on the icons below to view the QR code for each e-wallet service:</p>
            <div class="qr-options">
                <button onclick="showQRCode('gcash')"><i class="fas fa-qrcode"></i> Gcash</button>
                <button onclick="showQRCode('paymaya')"><i class="fas fa-qrcode"></i> Paymaya</button>
                <button onclick="showQRCode('paypal')"><i class="fas fa-qrcode"></i> PayPal</button>
            </div>
            <div class="qr-code-display">
                <img id="gcashQRCode" src="./src/images/gcash.png" alt="Gcash QR Code" class="qr-code">
                <img id="paymayaQRCode" src="./src/images/paymaya.png" alt="Paymaya QR Code" class="qr-code" style="display: none;">
                <img id="paypalQRCode" src="./src/images/paypal.png" alt="PayPal QR Code" class="qr-code" style="display: none;">
            </div>
            <p><strong>Gcash ID:</strong> 09123456789</p>
            <p><strong>Paymaya ID:</strong> 09876543210</p>
            <p><strong>PayPal ID:</strong> yourname@example.com</p>
        </div>

        <!-- Blockchain Donation Options -->
        <div class="donation-options">
            <h3>Blockchain Donation Options:</h3>
            <p>You can also support us via blockchain! Here are our addresses for different chains:</p>
            <p><strong>BNB Chain:</strong> 0x123456789ABCDEF123456789ABCDEF1234567890</p>
            <p><strong>Ethereum Chain:</strong> 0x0987654321ABCDEF0987654321ABCDEF09876543</p>
            <p><strong>Polygon Chain:</strong> 0xABCDE123456789ABCDE123456789ABCDE1234567</p>
        </div>

        <p>Any amount you choose to give is greatly appreciated and will go directly towards making this system the best resource it can be for students like you. Thank you for considering a donation!</p>
    </div>
</div>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM6h9lA4KX0v5ZCrA2MoDGR9mQ4D9GVH8iv7v+1" crossorigin="anonymous">

<style>
    /* Modal styling */
    #donationModal.modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #donationModal .modal-content {
        background-color: #1e1e2e;
        padding: 30px;
        border: none;
        width: 90%;
        max-width: 500px;
        max-height: 90vh; /* Restrict modal height */
        overflow-y: auto; /* Enable scrolling if content overflows */
        text-align: center;
        border-radius: 10px;
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);
        position: relative;
        color: #f1f1f1;
    }

    #donationModal .close {
        color: #888;
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
    }

    #donationModal .close:hover {
        color: #f1f1f1;
    }

    #donationModal h2 {
        color: #ffcc00;
        font-size: 24px;
        margin-bottom: 20px;
    }

    #donationModal p {
        color: #f1f1f1;
        font-size: 15px;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    #donationModal .donation-options {
        text-align: center;
        margin: 20px 0;
    }

    #donationModal .qr-options {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    #donationModal .qr-options button {
        background-color: #444;
        border: none;
        padding: 10px 15px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #ffcc00;
        transition: background-color 0.3s, color 0.3s;
    }

    #donationModal .qr-options button:hover {
        background-color: #ffcc00;
        color: #444;
    }

    #donationModal .qr-code-display {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    #donationModal .qr-code {
        width: auto;
        max-width: 300px; /* Set a max width for larger screens */
        max-height: 300px; /* Set a max height */
        border-radius: 5px;
        box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.2);
    }

    /* Scrollbar styling for the modal content */
    #donationModal .modal-content::-webkit-scrollbar {
        width: 8px;
    }

    #donationModal .modal-content::-webkit-scrollbar-track {
        background: #333;
        border-radius: 10px;
    }

    #donationModal .modal-content::-webkit-scrollbar-thumb {
        background-color: #ffcc00;
        border-radius: 10px;
        border: 2px solid #333;
    }

    /* Firefox scrollbar styling */
    #donationModal .modal-content {
        scrollbar-width: thin;
        scrollbar-color: #ffcc00 #333;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #donationModal .modal-content {
            width: 90%;
            padding: 20px;
        }

        #donationModal h2 {
            font-size: 20px;
        }

        #donationModal p {
            font-size: 14px;
        }

        #donationModal .qr-code {
            max-width: 200px;
            max-height: 200px;
        }
    }

    @media (max-width: 500px) {
        #donationModal .modal-content {
            width: 95%;
            padding: 15px;
            max-width: 400px;
        }

        #donationModal h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        #donationModal p {
            font-size: 13px;
        }

        #donationModal .qr-code {
            max-width: 150px;
            max-height: 150px;
        }
    }
</style>



<script>
    function showQRCode(wallet) {
        document.getElementById("gcashQRCode").style.display = "none";
        document.getElementById("paymayaQRCode").style.display = "none";
        document.getElementById("paypalQRCode").style.display = "none";

        document.getElementById(wallet + "QRCode").style.display = "block";
    }

    function closeModal() {
        document.getElementById("donationModal").style.display = "none";
    }

    // Check if PHP variable for showing the modal is true
    <?php if ($showModal): ?>
    window.onload = function() {
        document.getElementById("donationModal").style.display = "flex";
        showQRCode('gcash'); // Show Gcash QR code by default
    };
    <?php endif; ?>
</script>







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
                                $sql = "SELECT COUNT(*) AS total_borrowed FROM borrow WHERE status = 'pending' and student_id = $Student_Id"; // Replace 'books' with your table name and 'status' with your field
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
                                <p class="text-sm font-medium text-muted-foreground">TOTAL BORROWED BOOKS</p>
                                <div class="flex items-center text-red-600">

                                </div>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <?php

                                // Prepared statement to prevent SQL injection


                                // Query to count borrowed books
                                $sql = "SELECT COUNT(*) AS total_borrowed FROM borrow WHERE status = 'borrowed' and student_id = $Student_Id"; // Replace 'books' with your table name and 'status' with your field
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
                    $dbname1 = "GFI_Library_Database";
                    $username = "root";
                    $password = "";

                    try {
                        $pdo1 = new PDO("mysql:host=$host;dbname=$dbname1", $username, $password);
                        $pdo1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    } catch (PDOException $e) {
                        die("Connection failed: " . $e->getMessage());
                    }

                    // Second database connection
                    $host2 = "localhost";
                    $dbname2 = "gfi_library_database_books_records";
                    $username2 = "root";
                    $password2 = "";

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
    FROM GFI_Library_Database.most_borrowed_books
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
                            $titleQuery = "SELECT title FROM gfi_library_database_books_records.`$category` WHERE id = :book_id";
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