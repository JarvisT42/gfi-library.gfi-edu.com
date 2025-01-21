<?php
// Start session
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');

    exit;
}


// If book_bag session is not set, initialize it as an empty array
if (!isset($_SESSION['book_bag'])) {
    $_SESSION['book_bag'] = [];
}

// Get book bag data
$bookBag = $_SESSION['book_bag'];

// Count of items in the book bag
$bookBagCount = count($bookBag);
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







<body>
    <?php include './src/components/sidebar.php'; ?>





    <div class="p-4 sm:ml-64 custom-container">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 flex flex-col gap-4">

            <div class="mb-4 mt-8 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                <h2 class="text-1xl font-semibold">Step 2: Review Your Selection</h2>
                Review the books you have added to your book bag.
            </div>



            <div class="scrollable-table-container border border-gray-200 dark:border-gray-700">
                <div class="container mx-auto px-4 py-6">
                    <ul class="flex flex-col space-y-4">
                        <?php foreach ($bookBag as $index => $book): ?>
                            <li class="p-4 bg-white flex flex-col md:flex-row items-start border-b-2 border-black">
                                <div class="flex flex-col md:flex-row items-start w-full space-y-4 md:space-y-0 md:space-x-6">
                                    <!-- Text Content -->
                                    <div class="flex-1 w-full md:w-auto">
                                        <h2 class="text-lg font-semibold mb-2">
                                            <a href="#" class="text-blue-600 hover:underline max-w-xs break-words">
                                                <?php echo htmlspecialchars($book['title']); ?>
                                            </a>
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

                                    <!-- Remove to Book Bag Link -->
                                    <div class="flex-shrink-0 ml-2">
                                        <a href="#" class="text-green-600 hover:underline remove-book" data-title="<?php echo htmlspecialchars($book['title']); ?>" data-author="<?php echo htmlspecialchars($book['author']); ?>" data-publication="<?php echo htmlspecialchars($book['publicationDate']); ?>" data-table="<?php echo htmlspecialchars($book['table']); ?>" data-cover="<?php echo htmlspecialchars($book['coverImage']); ?>" data-copies="<?php echo htmlspecialchars($book['copies']); ?>">
                                            <span class="fa fa-plus"></span> Remove to Book Bag
                                        </a>
                                    </div>

                                    <!-- Cover Image -->
                                    <div class="flex-shrink-0">
                                        <img src="<?php echo htmlspecialchars($book['coverImage']); ?>" alt="Book Cover" class="w-36 h-56 border-2 border-gray-400 rounded-lg object-cover transition-transform duration-200 transform hover:scale-105">
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>


                    <!-- Next Button -->
                    <!-- Next Button -->
                    <div class="mt-8 bg-white border border-gray-300 w-full p-4 rounded-lg shadow-md flex justify-end">
                        <button id="nextButton" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Next
                        </button>
                    </div>

                    <script>
                        document.getElementById('nextButton').addEventListener('click', function(event) {
                            <?php if (empty($bookBag)): ?>
                                event.preventDefault(); // Prevent the default action
                                alert('Cannot proceed. The book bag is empty.');
                            <?php else: ?>
                                // Redirect to the next page or perform your next action
                                window.location.href = 'schedule.php'; // Replace with your next page URL
                            <?php endif; ?>
                        });
                    </script>

                </div>
            </div>
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