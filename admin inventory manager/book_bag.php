<?php
// Start session
session_start();

// If book_bag session is not set, initialize it as an empty array

if (!isset($_SESSION['logged_Admin_assistant']) || $_SESSION['logged_Admin_assistant'] !== true) {
    header('Location: ../index.php');

    exit;
}



if (!isset($_SESSION['book_bag'])) {
    $_SESSION['book_bag'] = [];
}

// Get book bag data
$bookBag = $_SESSION['book_bag'];

// Count of items in the book bag
$bookBagCount = count($bookBag);

// Retrieve the student name from the URL parameter
$fullName = isset($_GET['full_name']) ? htmlspecialchars($_GET['full_name']) : '';
$dueDate = isset($_GET['due_date']) ? htmlspecialchars($_GET['due_date']) : '';
$role = isset($_GET['role']) ? htmlspecialchars($_GET['role']) : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Details</title>
    <link rel="stylesheet" href="borrow.css">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->

    <style>
        /* Custom height for container */
        .custom-container {
            height: auto;
        }
    </style>
</head>







<body class="bg-gray-100">
    <?php include './src/components/sidebar.php'; ?>





    <div class="p-4 sm:ml-64 custom-container">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 flex flex-col gap-4">
            <h1 class="text-lg font-semibold">Borrow </h1>



            <form id="borrowForm" method="POST">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-start space-x-4">
                    <label for="role" class="text-left">ROLE:&nbsp;&nbsp;&nbsp;</label>
                    <input id="role" name="role" value="<?php echo $role; ?>" class="col-span-2 border rounded px-3 py-2" readonly />

                    <label for="full_name" class="text-left">FULL NAME:&nbsp;&nbsp;&nbsp;</label>
                    <input id="full_name" name="full_name" value="<?php echo $fullName; ?>" class="col-span-2 border rounded px-3 py-2" readonly />

                    <label for="date" class="text-left">DUE DATE:</label>
                    <input type="date" id="due_date" name="due_date" value="<?php echo $dueDate; ?>" class="border rounded px-3 py-2" readonly />
                </div>

                <div class="scrollable-table-container border border-gray-200 dark:border-gray-700">
                    <div class="container mx-auto px-4 py-6">
                        <ul class="flex flex-col space-y-4">
                            <?php foreach ($bookBag as $index => $book): ?>
                                <li class="p-4 bg-white flex flex-col md:flex-row items-start border-b-2 border-black">
                                    <div class="flex flex-col md:flex-row items-start w-full space-y-4 md:space-y-0 md:space-x-6">
                                        <div class="flex-1 w-full md:w-auto">
                                            <h2 class="text-lg font-semibold mb-2">
                                                <a href="#" class="text-blue-600 hover:underline max-w-xs break-words">
                                                    <?php echo htmlspecialchars($book['title']); ?>

                                                </a>

                                                <div class="mt-2">
                                                    <label for="accession-dropdown-<?php echo htmlspecialchars($book['id']); ?>" class="text-sm font-medium text-gray-700">Select Accession No:</label>
                                                    <select id="accession-dropdown-<?php echo htmlspecialchars($book['id']); ?>" name="accession_no[<?php echo htmlspecialchars($book['id']); ?>]" class="ml-2 border border-gray-300 rounded-md p-1" required>
                                                        <?php
                                                        include '../connection.php'; // Ensure you have your database connection

                                                        $accessionQuery = "SELECT accession_no FROM `accession_records` WHERE book_id = ? AND book_category = ? AND available NOT IN ('no', 'reserved')";
                                                        $stmt3 = $conn->prepare($accessionQuery);
                                                        $stmt3->bind_param("is", htmlspecialchars($book['id']), htmlspecialchars($book['table']));
                                                        $stmt3->execute();
                                                        $accessionResult = $stmt3->get_result();

                                                        while ($accessionRow = $accessionResult->fetch_assoc()) {
                                                            echo '<option value="' . htmlspecialchars($accessionRow['accession_no']) . '">' . htmlspecialchars($accessionRow['accession_no']) . '</option>';
                                                        }
                                                        $stmt3->close();
                                                        ?>
                                                    </select>
                                                </div>
                                            </h2>


                                            <div class="mt-4">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 text-sm text-gray-600">
                                                    <div class="font-medium bg-gray-200 p-2">Main Author:</div>
                                                    <div class="bg-gray-100 p-2"><?php echo htmlspecialchars($book['author']); ?></div>
                                                    <div class="font-medium bg-gray-100 p-2">Published:</div>
                                                    <div class="bg-gray-200 p-2"><?php echo htmlspecialchars($book['publicationDate']); ?></div>
                                                    <div class="font-medium bg-gray-200 p-2">Table:</div>
                                                    <div class="bg-gray-100 p-2"><?php echo htmlspecialchars($book['table']); ?></div>
                                                    
                                                    <div class="font-medium bg-gray-100 p-2">Copies:</div>
                                                    <div class="bg-gray-200 p-2"><?php echo htmlspecialchars($book['copies']); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ml-2">
                                            <a href="#" class="text-green-600 hover:underline remove-book" data-title="<?php echo htmlspecialchars($book['title']); ?>" data-author="<?php echo htmlspecialchars($book['author']); ?>" data-publication="<?php echo htmlspecialchars($book['publicationDate']); ?>" data-table="<?php echo htmlspecialchars($book['table']); ?>" data-cover="<?php echo htmlspecialchars($book['coverImage']); ?>" data-copies="<?php echo htmlspecialchars($book['copies']); ?>">
                                                <span class="fa fa-plus"></span> Remove to Book Bag
                                            </a>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <img src="<?php echo htmlspecialchars($book['coverImage']); ?>" alt="Book Cover" class="w-36 h-56 border-2 border-gray-400 rounded-lg object-cover transition-transform duration-200 transform hover:scale-105">
                                        </div>
                                        <input type="hidden" name="table[<?php echo htmlspecialchars($book['id']); ?>]" value="<?php echo htmlspecialchars($book['table']); ?>">

                                    </div>
                                </li>
                            <?php endforeach; ?>

                        </ul>

                        <div class="mt-8 bg-white border border-gray-300 w-full p-4 rounded-lg shadow-md flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Borrow
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <script>
                document.getElementById('borrowForm').addEventListener('submit', function(event) {
                    event.preventDefault(); // Prevent the default form submission

                    const formData = new FormData(this); // Create a FormData object

                    // Collect data to display in an alert
                    let dataToSend = 'Data to be sent:\n';
                    formData.forEach((value, key) => {
                        dataToSend += `${key}: ${value}\n`; // Append each key-value pair to the dataToSend string
                    });
 

                    // Show alert with all the data
                    // alert(dataToSend);

                    // Send the form data using fetch
                    fetch('book_bag_save.php', {
                            method: 'POST',
                            body: formData,
                        })
                        .then(response => {
                            if (response.ok) {
                                return response.json(); // Parse JSON response
                            } else {
                                throw new Error('Network response was not ok.');
                            }
                        })
                        .then(data => {
                            // Show alerts based on the response from PHP
                            if (data.status === 'success') {
                                alert(data.message); // Show success alert
                                window.location.href = 'borrow.php'; // Redirect to borrow.php
                            } else {
                                alert(data.message); // Show error alert
                            }
                        })
                        .catch(error => {
                            console.error('There was a problem with the fetch operation:', error);
                        });
                });
            </script>



        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const removeLinks = document.querySelectorAll('.remove-book');

            removeLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();

                    const bookData = {
                        title: this.getAttribute('data-title'),
                        author: this.getAttribute('data-author'),
                        publicationDate: this.getAttribute('data-publication'),
                        table: this.getAttribute('data-table'),
                        coverImage: this.getAttribute('data-cover'),
                        copies: this.getAttribute('data-copies')
                    };

                    fetch('remove_from_book_bag.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(bookData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Optionally, you can remove the book from the UI
                                this.closest('li').remove();
                                location.reload();
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>




</body>

</html>