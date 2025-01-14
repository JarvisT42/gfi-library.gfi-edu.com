<?php
# Initialize the session
session_start();
include '../connection.php';  // Assuming you have a connection script

// Get the current date
$currentDate = new DateTime();

// Query to get all pending borrow records
$sql = "SELECT * FROM borrow WHERE status = 'pending'";
$result = $conn->query($sql);

$nearestRow = null; // To store the row with the nearest date
$nearestDiff = null; // To store the difference in days

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dateToClaim = new DateTime($row['Date_To_Claim']);
        
        // Calculate the difference between the date to claim and today's date
        $diff = $dateToClaim->diff($currentDate)->days;
        
        // Check if the date is in the future (or today) and if it's the nearest one so far
        if ($dateToClaim >= $currentDate && (is_null($nearestDiff) || $diff < $nearestDiff)) {
            $nearestRow = $row;
            $nearestDiff = $diff;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="path/to/your/styles.css">
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <!-- Include Flowbite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.js"></script>

    <style>
        .active-book-request {
            background-color: #f0f0f0;
            color: #000;
        }
    </style>
</head>

<body>
    <?php include './src/components/sidebar.php'; ?>

    <main id="content" class="">
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 min-h-screen border-gray-200 border-dashed rounded-lg dark:border-gray-700">
                <div class="relative min-h-screen overflow-x-auto shadow-md sm:rounded-lg p-6">
                    <div class="flex flex-col sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
                        <label for="table-search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input type="text" id="table-search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for items">
                        </div>
                    </div>

                    <div class="overflow-y-auto max-h-screen">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border border-gray-300">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-10">
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-300">Student Name</th>
                                    <th class="px-6 py-3 border-b border-gray-300">Course</th>
                                    <th class="px-6 py-3 border-b border-gray-300">Borrow Books</th>
                                    <th class="px-6 py-3 border-b border-gray-300">Date To Claim</th>
                                    <th class="px-6 py-3 border-b border-gray-300">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!is_null($nearestRow)): ?>
                                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-300">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($nearestRow['Student']); ?>
                                        </th>
                                        <td class="px-6 py-4">
                                            <?php echo htmlspecialchars($nearestRow['Course']); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php echo htmlspecialchars($nearestRow['Borrow_Books']); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $date = new DateTime($nearestRow['Date_To_Claim']);
                                            $formattedDate = $date->format('F j, Y') . ' - ' . $date->format('l');
                                            echo htmlspecialchars($formattedDate) . ' ' . htmlspecialchars($nearestRow['Time']);
                                            ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                                Next
                                            </button>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center">No records found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>

<?php
$conn->close(); // Close the database connection
?>
