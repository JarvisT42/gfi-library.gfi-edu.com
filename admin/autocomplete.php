<?php
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <style>
        .active-books {
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


                        <li><a class="px-4 py-2 " href="books.php">All</a></li>
                        <br>
                        <li><a class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" href="add_books.php">Add Books</a></li>
                        <br>
                        <li><a class="px-4 py-2 " href="edit_records.php">Edit Records</a></li>
                        <br>
                        <li><a class="px-4 py-2" href="damage.php">Damage Books</a></li>
                        <br>
                        <li><a class="px-4 py-2 " href="subject_for_replacement.php">Subject For Replacement</a></li>


                        <br>
                        <!-- <li><a href="#">Subject for Replacement</a></li> -->
                    </ul> <!-- Button beside the title -->


                </div>

                <!-- Main Content Box -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 ">

                    <div class="w-full max-w-2xl mx-auto border border-black  rounded-t-lg">
                        <div class="bg-red-800 text-white rounded-t-lg">
                            <h2 class="text-lg font-semibold p-4">Please Enter Details Below</h2>
                        </div>
                        <div class="p-6 bg-white rounded-b-lg shadow-md">










                            <form id="categoryForm" class="space-y-6" method="POST" enctype="multipart/form-data">
                                <!-- CATEGORY -->


                                <!-- CATEGORY -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4 bg-blue-50 border border-blue-300 p-4 rounded-md">
                                    <label for="category" class="text-left font-medium text-blue-700">CATEGORY:</label>
                                    <?php
                                    include("../connection.php");
                                    $sql = "SHOW TABLES FROM dnllaaww_gfi_library_books_inventory";
                                    $result = mysqli_query($conn, $sql);
                                    ?>
                                    <select id="category" class="col-span-2 border border-blue-400 rounded px-3 py-2 bg-white text-blue-800" name="table">
                                        <option value="" disabled selected>Select Category</option>
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_array()) {
                                                $tableName = $row[0];
                                                echo '<option value="' . htmlspecialchars($tableName) . '">' . htmlspecialchars($tableName) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- ADD CATEGORY -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4 bg-yellow-50 border border-yellow-300 p-4 rounded-md">
                                    <label for="checkbox_id" class="text-left font-medium text-yellow-700">ADD CATEGORY:</label>
                                    <div class="col-span-2 flex items-center gap-2">
                                        <input type="checkbox" id="checkbox_id" name="add_category_checkbox" class="mr-2 border border-yellow-400 bg-yellow-100 rounded text-yellow-700" />
                                        <input id="add_category" name="add_category" placeholder="Add Category" class="border border-yellow-400 bg-yellow-100 rounded px-3 py-2 w-full text-yellow-800" disabled />
                                    </div>
                                </div>


                                <!-- CALL NUMBER -->

                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                    <label for="call_number" class="text-left">CALL NUMBER: <span class="text-red-600">*</span></label>
                                    <input id="call_number" name="call_number" placeholder="Call Number (required)" class="col-span-2 border rounded px-3 py-2 w-full" required />
                                </div>

                                <!-- BOOK TITLE -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                    <label for="book_title" class="text-left">BOOK TITLE: <span class="text-red-600">*</span></label>
                                    <input id="book_title" name="book_title" placeholder="Book Title (required)" class="col-span-2 border rounded px-3 py-2 w-full" required />
                                </div>

                                <!-- AUTHOR -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                    <label for="author" class="text-left">AUTHOR: <span class="text-red-600">*</span></label>
                                    <input id="author" name="author" placeholder="Author (required)" class="col-span-2 border rounded px-3 py-2 w-full" required />
                                </div>

                                <script>
                                    $(document).ready(function() {
                                        // Autocomplete for Book Title
                                        $("#book_title").autocomplete({
                                            source: function(request, response) {
                                                $.ajax({
                                                    url: "add_books_fetch_data.php", // Path to your PHP script
                                                    dataType: "json",
                                                    data: {
                                                        term: request.term, // Send the typed input as a parameter
                                                        type: "book_title" // Type of field to search
                                                    },
                                                    success: function(data) {
                                                        response(data); // Return the titles to the input field
                                                    }
                                                });
                                            },
                                            minLength: 1 // Trigger autocomplete after 1 character
                                        });

                                        // Autocomplete for Call Number
                                        $("#call_number").autocomplete({
                                            source: function(request, response) {
                                                $.ajax({
                                                    url: "add_books_fetch_data.php", // Path to your PHP script
                                                    dataType: "json",
                                                    data: {
                                                        term: request.term, // Send the typed input as a parameter
                                                        type: "call_number" // Type of field to search
                                                    },
                                                    success: function(data) {
                                                        response(data); // Return the call numbers to the input field
                                                    }
                                                });
                                            },
                                            minLength: 1 // Trigger autocomplete after 1 character
                                        });

                                        // Autocomplete for Author
                                        $("#author").autocomplete({
                                            source: function(request, response) {
                                                $.ajax({
                                                    url: "add_books_fetch_data.php", // Path to your PHP script
                                                    dataType: "json",
                                                    data: {
                                                        term: request.term, // Send the typed input as a parameter
                                                        type: "author" // Type of field to search
                                                    },
                                                    success: function(data) {
                                                        response(data); // Return the authors to the input field
                                                    }
                                                });
                                            },
                                            minLength: 1 // Trigger autocomplete after 1 character
                                        });
                                    });
                                </script>


                                <!-- YEAR OF PUBLICATION -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                    <label for="date_of_publication_copyright" class="text-left">Year of Publication (Copyright):</label>
                                    <select id="date_of_publication_copyright" name="date_of_publication_copyright" class="col-span-2 border rounded px-3 py-2">
                                        <option value="">Select Year</option>
                                        <?php
                                        $current_year = date("Y");
                                        for ($year = $current_year; $year >= 2000; $year--) {
                                            echo "<option value=\"$year\">$year</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- BOOK COPIES -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                    <label for="book_copies" class="text-left">BOOK COPIES:</label>
                                    <div class="col-span-2 flex items-center gap-2">
                                        <button id="decrementBtn" type="button" class="bg-blue-500 text-white p-2 rounded">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input id="book_copies" name="book_copies" type="number" class="border rounded px-3 py-2 w-16 text-center no-spinner" value="0" />
                                        <button id="incrementBtn" type="button" class="bg-blue-500 text-white p-2 rounded">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>

                                    <style>
                                        /* Hide the number input spinner controls */
                                        input[type="number"]::-webkit-inner-spin-button,
                                        input[type="number"]::-webkit-outer-spin-button {
                                            -webkit-appearance: none;
                                            margin: 0;
                                        }

                                        input[type="number"] {
                                            -moz-appearance: textfield;
                                            /* Firefox */
                                        }
                                    </style>

                                </div>
                                <div id="accessionNumberContainer"></div>









                                <!-- SAVE BUTTON -->
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









                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const checkbox = document.getElementById('checkbox_id');
                                    const categorySelect = document.getElementById('category');
                                    const addCategoryInput = document.getElementById('add_category');

                                    checkbox.addEventListener('change', function() {
                                        if (checkbox.checked) {
                                            categorySelect.value = "";
                                            categorySelect.disabled = true;
                                            addCategoryInput.disabled = false;
                                        } else {
                                            categorySelect.disabled = false;
                                            addCategoryInput.disabled = true;
                                            addCategoryInput.value = "";
                                        }
                                    });

                                    const form = document.getElementById('categoryForm');
                                    form.addEventListener('submit', function(event) {
                                        event.preventDefault();

                                        const warningContainer = document.getElementById('warningContainer');
                                        warningContainer.innerHTML = ''; // Clear previous warnings

                                        const formData = new FormData(form);

                                        // Handle borrowable checkbox value
                                        const borrowableCheckbox = document.getElementById('borrowable');
                                        if (borrowableCheckbox.checked) {
                                            formData.append('borrowable', 'yes'); // Set to 'yes' when checked
                                        } else {
                                            formData.append('borrowable', 'no'); // Set to 'no' when not checked
                                        }

                                        // Send the form data using fetch
                                        fetch('add_books_handle_category.php', {
                                                method: 'POST',
                                                body: formData,
                                            })
                                            .then((response) => response.json())
                                            .then((data) => {
                                                if (data.status === 'success') {
                                                    alert(data.message);
                                                    window.location.reload();
                                                } else {
                                                    document.getElementById('warningContainer').innerHTML = `<p>Error: ${data.message}</p>`;
                                                    console.error(`PHP Error: ${data.message}`);
                                                }
                                            })
                                            .catch((error) => {
                                                console.error('There was a problem with the fetch operation:', error);
                                                document.getElementById('warningContainer').innerHTML = `<p>Fetch error: ${error.message}</p>`;
                                            });
                                    });
                                });
                            </script>











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