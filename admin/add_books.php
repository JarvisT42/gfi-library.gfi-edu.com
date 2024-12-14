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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="borrow.css">
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
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







                            <form id="categoryForm" class="space-y-4" method="POST" enctype="multipart/form-data">
                                <div class="grid grid-cols-7 items-center gap-4 mt-3">
                                    <label for="category" class="text-left">CATEGORY:</label>
                                    <?php
                                    include("../connection.php");
                                    $sql = "SHOW TABLES FROM dnllaaww_gfi_library_books_inventory";
                                    $result = mysqli_query($conn, $sql);
                                    ?>
                                    <select id="category" class="col-span-2 border rounded px-3 py-2" name="table">
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
                                    <div class="flex items-center col-span-2">
                                        <input type="checkbox" id="checkbox_id" name="add_category_checkbox" class="mr-2" />
                                        <label for="checkbox_id" class="text-left">ADD CATEGORY:</label>
                                    </div>
                                    <input id="add_category" name="add_category" placeholder="Add Category" class="col-span-2 border rounded px-3 py-2" disabled />
                                </div>





                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="call_number" class="text-left">CALL NUMBER: <span class="text-red-600">*</span></label>
                                    <div class="col-span-2 relative">
                                        <input
                                            id="call_number"
                                            name="call_number"
                                            placeholder="Call Number (required)"
                                            class="w-full border rounded px-3 py-2"
                                            required />
                                        <div id="callNumberSuggestions"></div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="book_title" class="text-left">BOOK TITLE: <span class="text-red-600">*</span></label>
                                    <div class="col-span-2 relative">
                                        <input
                                            id="book_title"
                                            name="book_title"
                                            placeholder="Book Title (required)"
                                            class="w-full border rounded px-3 py-2"
                                            required />
                                        <div id="titleSuggestions"></div>
                                    </div>
                                </div>

                                <style>
                                    #callNumberSuggestions,
                                    #titleSuggestions,
                                    #authorSuggestions {
                                        position: absolute;
                                        z-index: 10;
                                        width: 100%;
                                        background-color: #ffffff;
                                        border-radius: 4px;
                                        max-height: 200px;
                                        overflow-y: auto;
                                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                                    }

                                    #callNumberSuggestions a,
                                    #titleSuggestions a,
                                    #authorSuggestions a {
                                        display: block;
                                        padding: 10px;
                                        cursor: pointer;
                                        text-decoration: none;
                                        color: inherit;
                                    }

                                    #callNumberSuggestions a:hover,
                                    #titleSuggestions a:hover,
                                    #authorSuggestions a:hover {
                                        background-color: #f1f1f1;
                                    }
                                </style>






                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="author" class="text-left">AUTHOR: <span class="text-red-600">*</span></label>
                                    <div class="col-span-2 relative">
                                        <input
                                            id="author"
                                            name="author"
                                            placeholder="Author (required)"
                                            class="w-full border rounded px-3 py-2"
                                            required />
                                        <div id="authorSuggestions"></div>
                                    </div>
                                </div>





                             <div class="grid grid-cols-3 items-center gap-4">
    <label for="date_of_publication_copyright" class="text-left">Year of Publication (Copyright)</label>
    <select id="date_of_publication_copyright" name="date_of_publication_copyright" class="col-span-2 border rounded px-3 py-2">
        <option value="">Select Year</option>
        <?php
        // Example: Generate a list of years dynamically (from current year to 2000)
        $current_year = date("Y"); // Get the current year
        for ($year = $current_year; $year >= 2000; $year--) {
            echo "<option value=\"$year\">$year</option>";
        }
        ?>
    </select>
</div>





                             <div class="grid grid-cols-3 items-center gap-4">
    <label for="book_copies" class="text-left">BOOK COPIES:</label>
    <input id="book_copies" name="book_copies" type="number" class="col-span-2 border rounded px-3 py-2" value="0" />
</div>


                                <div id="accessionNumberContainer" class="space-y-2"></div>

                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="publisher_name" class="text-left">PUBLISHER NAME:</label>
                                    <input id="publisher_name" name="publisher_name" placeholder="Publisher Name" class="col-span-2 border rounded px-3 py-2" />
                                </div>



                               <div class="grid grid-cols-3 items-center gap-4">
    <label for="price" class="text-left">PRICE:</label>
    <input
        id="price"
        name="price"
        placeholder="Price (in PHP)"
        type="number"
        step="0.01"
        class="col-span-2 border rounded px-3 py-2"
        value="0" />
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
                                <div id="warningContainer" class="text-red-600 mt-2"></div>

                            </form>


                            <script>
                                document.getElementById("book_copies").addEventListener("input", function() {
                                    const accessionContainer = document.getElementById("accessionNumberContainer");
                                    accessionContainer.innerHTML = ''; // Clear existing fields

                                    const copiesCount = parseInt(this.value, 10);
                                    if (copiesCount > 0) {
                                        // Create the specified number of Accession Number fields
                                        for (let i = 1; i <= copiesCount; i++) {
                                            const accessionDiv = document.createElement("div");
                                            accessionDiv.classList.add("grid", "grid-cols-3", "items-center", "gap-4");

                                            const label = document.createElement("label");
                                            label.textContent = `ACCESSION NO ${i}:`;
                                            label.classList.add("text-left");

                                            const input = document.createElement("input");
                                            input.type = "text";
                                            input.name = `accession_no_${i}`;
                                            input.placeholder = `Accession Number ${i}`;
                                            input.classList.add("col-span-2", "border", "rounded", "px-3", "py-2");

                                            accessionDiv.appendChild(label);
                                            accessionDiv.appendChild(input);
                                            accessionContainer.appendChild(accessionDiv);
                                        }
                                    }
                                });
                            </script>

                            <script>
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

                                    // Check for duplicate accession numbers
                                    const accessionNumbers = [];
                                    let duplicateFound = false;
                                    formData.forEach((value, key) => {
                                        if (key.startsWith("accession_no_")) {
                                            if (accessionNumbers.includes(value)) {
                                                warningContainer.innerHTML = `<p>Duplicate accession number found: ${value}</p>`;
                                                duplicateFound = true;
                                            }
                                            accessionNumbers.push(value);
                                        }
                                    });

                                    // If duplicates are found, stop submission
                                    if (duplicateFound) return;

                                    // Send the form data using fetch if no duplicates
                                    fetch('add_books_handle_category.php', {
                                            method: 'POST',
                                            body: formData,
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.status === 'success') {
                                                alert(data.message);
                                                window.location.reload();
                                            } else {
                                                // Display error message in the warning container
                                                warningContainer.innerHTML = `<p>Error: ${data.message}</p>`;
                                                console.error(`PHP Error: ${data.message}`);
                                            }
                                        })
                                        .catch(error => {
                                            console.error('There was a problem with the fetch operation:', error);
                                            warningContainer.innerHTML = `<p>Fetch error: ${error.message}</p>`;
                                        });
                                });
                            </script>







<script>
                                    // Fetch suggestions for Call Number
                                    document.getElementById('call_number').addEventListener('input', function() {
                                        const query = this.value;
                                        const suggestionsContainer = document.getElementById('callNumberSuggestions');

                                        if (query.length >= 1) {
                                            fetch(`add_books_fetch_data.php?query=${query}`)
                                                .then(response => response.json())
                                                .then(data => {
                                                    suggestionsContainer.innerHTML = ''; // Clear previous suggestions

                                                    data.forEach(item => {
                                                        if (item.CallNumber) {
                                                            const suggestionItem = document.createElement('a');
                                                            suggestionItem.textContent = item.CallNumber;
                                                            suggestionItem.classList.add('list-group-item', 'list-group-item-action');
                                                            suggestionItem.addEventListener('click', function() {
                                                                document.getElementById('call_number').value = this.textContent;
                                                                suggestionsContainer.innerHTML = ''; // Clear suggestions after selection
                                                            });
                                                            suggestionsContainer.appendChild(suggestionItem);
                                                        }
                                                    });
                                                })
                                                .catch(error => console.error('Error fetching call numbers:', error));
                                        } else {
                                            suggestionsContainer.innerHTML = ''; // Clear suggestions if input is empty
                                        }
                                    });

                                    // Fetch suggestions for Book Title
                                    document.getElementById('book_title').addEventListener('input', function() {
                                        const query = this.value;
                                        const suggestionsContainer = document.getElementById('titleSuggestions');

                                        if (query.length >= 1) {
                                            fetch(`add_books_fetch_data.php?query=${query}`)
                                                .then(response => response.json())
                                                .then(data => {
                                                    suggestionsContainer.innerHTML = ''; // Clear previous suggestions

                                                    data.forEach(item => {
                                                        if (item.Title) {
                                                            const suggestionItem = document.createElement('a');
                                                            suggestionItem.textContent = item.Title;
                                                            suggestionItem.classList.add('list-group-item', 'list-group-item-action');
                                                            suggestionItem.addEventListener('click', function() {
                                                                document.getElementById('book_title').value = this.textContent;
                                                                suggestionsContainer.innerHTML = ''; // Clear suggestions after selection
                                                            });
                                                            suggestionsContainer.appendChild(suggestionItem);
                                                        }
                                                    });
                                                })
                                                .catch(error => console.error('Error fetching book titles:', error));
                                        } else {
                                            suggestionsContainer.innerHTML = ''; // Clear suggestions if input is empty
                                        }
                                    });

                                    // Fetch suggestions for Author
                                    document.getElementById('author').addEventListener('input', function() {
                                        const query = this.value;
                                        const suggestionsContainer = document.getElementById('authorSuggestions');

                                        if (query.length >= 1) {
                                            fetch(`add_books_fetch_data.php?query=${query}`)
                                                .then(response => response.json())
                                                .then(data => {
                                                    suggestionsContainer.innerHTML = ''; // Clear previous suggestions

                                                    data.forEach(item => {
                                                        if (item.Author) {
                                                            const suggestionItem = document.createElement('a');
                                                            suggestionItem.textContent = item.Author;
                                                            suggestionItem.classList.add('list-group-item', 'list-group-item-action');
                                                            suggestionItem.addEventListener('click', function() {
                                                                document.getElementById('author').value = this.textContent;
                                                                suggestionsContainer.innerHTML = ''; // Clear suggestions after selection
                                                            });
                                                            suggestionsContainer.appendChild(suggestionItem);
                                                        }
                                                    });
                                                })
                                                .catch(error => console.error('Error fetching authors:', error));
                                        } else {
                                            suggestionsContainer.innerHTML = ''; // Clear suggestions if input is empty
                                        }
                                    });

                                    // Close suggestions when clicking outside
                                    document.addEventListener('click', function(event) {
                                        // Get all input and suggestion containers
                                        const inputs = [{
                                                input: 'call_number',
                                                suggestions: 'callNumberSuggestions'
                                            },
                                            {
                                                input: 'book_title',
                                                suggestions: 'titleSuggestions'
                                            },
                                            {
                                                input: 'author',
                                                suggestions: 'authorSuggestions'
                                            },
                                        ];

                                        inputs.forEach(({
                                            input,
                                            suggestions
                                        }) => {
                                            const inputElement = document.getElementById(input);
                                            const suggestionsElement = document.getElementById(suggestions);

                                            // Check if the click happened outside both the input and its suggestions
                                            if (
                                                !inputElement.contains(event.target) &&
                                                !suggestionsElement.contains(event.target)
                                            ) {
                                                suggestionsElement.innerHTML = ''; // Clear suggestions
                                            }
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