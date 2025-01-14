<?php
session_start();
if (!isset($_SESSION['logged_Admin_assistant']) || $_SESSION['logged_Admin_assistant'] !== true) {
    header('Location: ../index.php');

    exit;
}


require '../connection2.php'; // Update with your actual database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $bookTitle = $_POST['book_title'];
    $author = $_POST['author'];
    $subject = $_POST['subject']; // New field for subject
    $link = $_POST['link'];

    // Check if an image was uploaded
    $imageData = null;
    if (!empty($_FILES['image']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
    }

    // Insert data into the database
    $stmt = $conn2->prepare("INSERT INTO `e-books` (title, author, subject, link, record_cover) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $bookTitle, $author, $subject, $link, $imageData);

    if ($stmt->execute()) {
        // Redirect or show a success message
        echo "<script>alert('E-Book added successfully!'); window.location.href = 'e-books.php';</script>";
    } else {
        // Handle errors
        echo "<script>alert('Error: Unable to add e-book. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn2->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="borrow.css">
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <style>
        .active-e-books {
            background-color: #f0f0f0;
            color: #000;
        }
    </style>
</head>

<body>
    <?php include './src/components/sidebar.php'; ?>

    <main id="content" class="">


        <div class="p-4 sm:ml-64">

            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">

                <!-- Title Box -->
                <!-- Title and Button Box -->
                <?php include './src/components/books.php'; ?>

                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <ul class="flex flex-wrap gap-2 p-5 border border-dashed rounded-md w-full">


                        <li><a class="px-4 py-2 " href="e-books.php">All</a></li>
                        <br>
                        <li><a class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" href="add_e-books.php">Add E-Books</a></li>



                        <br>

                        <!-- <li><a class="px-4 py-2 " href="subject_for_replacement.php">Subject for Replacement</a></li> -->
                    </ul> <!-- Button beside the title -->


                </div>





                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 ">

                    <div class="w-full max-w-2xl mx-auto border border-black  rounded-t-lg">
                        <div class="bg-red-800 text-white rounded-t-lg">
                            <h2 class="text-lg font-semibold p-4">Please Enter Details Below</h2>
                        </div>
                        <div class="p-6 bg-white rounded-b-lg shadow-md">







                        <form id="categoryForm" class="space-y-4" method="POST" enctype="multipart/form-data" action="">
    <div class="grid grid-cols-3 items-center gap-4">
        <label for="book_title" class="text-left">BOOK TITLE:</label>
        <input id="book_title" name="book_title" placeholder="Book Title" class="col-span-2 border rounded px-3 py-2" required />
    </div>

    <div class="grid grid-cols-3 items-center gap-4">
        <label for="author" class="text-left">AUTHOR:</label>
        <input id="author" name="author" placeholder="Author" class="col-span-2 border rounded px-3 py-2" required />
    </div>

    <div class="grid grid-cols-3 items-center gap-4">
        <label for="subject" class="text-left">SUBJECT:</label>
        <input id="subject" name="subject" placeholder="Subject" class="col-span-2 border rounded px-3 py-2" required />
    </div>

    <div class="grid grid-cols-3 items-center gap-4">
        <label for="link" class="text-left">LINK:</label>
        <input id="link" name="link" placeholder="Link" class="col-span-2 border rounded px-3 py-2" required />
    </div>

    <div class="grid grid-cols-3 items-center gap-4">
        <label for="image" class="text-left">UPLOAD IMAGE:</label>
        <input type="file" id="image" name="image" accept="image/*" class="col-span-2 border rounded" />
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                <polyline points="17 21 17 13 7 13 7 21" />
                <polyline points="7 3 7 8 15 8" />
            </svg>
            Save
        </button>
    </div>
</form>













                        </div>
                    </div>


                </div>




                <!-- Main Content Box -->




            </div>

        </div>
        </div>

    </main>

    <script src="./src/components/header.js"></script>

</body>

</html>