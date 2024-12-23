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


                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4 bg-blue-50 border border-blue-300 p-4 rounded-md">
    <!-- CATEGORY Selection -->
    <label for="category" class="text-left font-medium text-blue-700">CATEGORY:</label>
    <?php
    include("../connection2.php");
    $sql = "SHOW TABLES FROM dnllaaww_gfi_library_books_inventory";
    $result = mysqli_query($conn2, $sql);
    ?>
    <select id="category" class="col-span-2 border border-blue-400 rounded px-3 py-2 bg-white text-blue-800" name="table">
        <option value="" disabled selected>Select Category</option>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array()) {
                $tableName = $row[0];
                // Exclude the 'e-books' table
                if ($tableName !== 'e-books') {
                    echo '<option value="' . htmlspecialchars($tableName) . '">' . htmlspecialchars($tableName) . '</option>';
                }
            }
        }
        ?>
    </select>

    <!-- ADD CATEGORY Option -->
    <label for="checkbox_id" class="text-left font-medium text-yellow-700">ADD CATEGORY:</label>
    <div class="col-span-2 flex items-center gap-2">
        <input type="checkbox" id="checkbox_id" name="add_category_checkbox" class="mr-2 border border-yellow-400 bg-yellow-100 rounded text-yellow-700" />
        <input id="add_category" name="add_category" placeholder="Add Category" class="border border-yellow-400 bg-yellow-100 rounded px-3 py-2 w-full text-yellow-800" disabled />
    </div>
</div>




                                <!-- CALL NUMBER -->

                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                    <label for="call_number" class="text-left">CALL NUMBER: <span class="text-red-600">*</span></label>
                                    <div class="col-span-2">
                                        <input id="call_number" name="call_number" placeholder="Call Number (required)" class="border rounded px-3 py-2 w-full" list="call_number_datalist" required />
                                        <datalist id="call_number_datalist"></datalist>
                                        <small id="call_number_error" class="text-red-600" style="display: none;">This Call Number already exists. Please use a unique Call Number.</small>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                    <label for="book_title" class="text-left">BOOK TITLE: <span class="text-red-600">*</span></label>
                                    <div class="col-span-2">
                                        <input id="book_title" name="book_title" placeholder="Book Title (required)" class="border rounded px-3 py-2 w-full" list="book_title_datalist" required />
                                        <datalist id="book_title_datalist"></datalist>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                    <label for="author" class="text-left">AUTHOR: <span class="text-red-600">*</span></label>
                                    <div class="col-span-2">
                                        <input id="author" name="author" placeholder="Author (required)" class="border rounded px-3 py-2 w-full" list="author_datalist" required />
                                        <datalist id="author_datalist"></datalist>
                                    </div>
                                </div>


                                <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        // Setup autocomplete for a given input
                                        function setupAutocomplete(inputId, datalistId, type) {
                                            const inputElement = document.getElementById(inputId);
                                            const datalistElement = document.getElementById(datalistId);

                                            inputElement.addEventListener("input", () => {
                                                const value = inputElement.value.trim();
                                                if (value.length > 0) {
                                                    fetch(`add_books_fetch_data.php?term=${encodeURIComponent(value)}&type=${type}`)
                                                        .then(response => response.json())
                                                        .then(data => {
                                                            datalistElement.innerHTML = "";
                                                            data.forEach(item => {
                                                                const option = document.createElement("option");
                                                                option.value = item;
                                                                datalistElement.appendChild(option);
                                                            });
                                                        })
                                                        .catch(error => console.error(`Autocomplete error for ${type}:`, error));
                                                }
                                            });
                                        }

                                        // Setup autocomplete for Call Number, Book Title, and Author
                                        setupAutocomplete("call_number", "call_number_datalist", "call_number");
                                        setupAutocomplete("book_title", "book_title_datalist", "book_title");
                                        setupAutocomplete("author", "author_datalist", "author");

                                        // Real-time validation for duplicate Call Number
                                        const callNumberInput = document.getElementById("call_number");
                                        const callNumberError = document.getElementById("call_number_error");

                                        callNumberInput.addEventListener("blur", () => {
                                            const callNumber = callNumberInput.value.trim();
                                            if (callNumber.length > 0) {
                                                fetch(`add_books_fetch_data.php?call_number=${encodeURIComponent(callNumber)}`)
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.isDuplicate) {
                                                            callNumberError.style.display = "block";
                                                            callNumberInput.classList.add("border-red-600");
                                                        } else {
                                                            callNumberError.style.display = "none";
                                                            callNumberInput.classList.remove("border-red-600");
                                                        }
                                                    })
                                                    .catch(error => console.error("Validation error:", error));
                                            } else {
                                                callNumberError.style.display = "none";
                                                callNumberInput.classList.remove("border-red-600");
                                            }
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

                                <!-- IMAGE UPLOAD -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                    <label for="publisher_name" class="text-left">PUBLISHER NAME:</label>
                                    <div class="col-span-2 relative">
                                        <input id="publisher_name" name="publisher_name" placeholder="Publisher Name" class="w-full border rounded px-3 py-2" required />

                                    </div>



                                </div>




                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                    <label for="price" class="text-left">PRICE:</label>
                                    <div class="col-span-2 relative">

                                        <input id="price" name="price" placeholder="Price (in PHP)" type="number" step="0.01" class="w-full border rounded px-3 py-2" value="0" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="borrowable" class="text-left">SET AS BORROWABLE:</label>
                                    <input
                                        id="borrowable"
                                        name="borrowable"
                                        type="checkbox"
                                        class="col-span-2 border rounded px-3 py-2" />
                                </div>


                                <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                    <label for="image" class="text-left">UPLOAD IMAGE:</label>
                                    <div class="col-span-2 relative">
                                        <input type="file" id="image" name="image" accept="image/*" class="w-full border rounded" />
                                        <small id="imageError" class="text-red-600" style="display:none;">Please upload an image of type JPG, JPEG, or PNG. Maximum file size is 10MB.</small>
                                        <!-- Description Text -->
                                        <p class="text-gray-500 text-sm mt-2">Please upload an image of type JPG, JPEG, or PNG. The file size should not exceed 10MB.</p>
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const imageInput = document.getElementById('image');
                                        const imageError = document.getElementById('imageError');

                                        imageInput.addEventListener('change', function() {
                                            const file = imageInput.files[0];
                                            if (file) {
                                                const fileType = file.type;
                                                const fileSize = file.size;

                                                // Validate file type (jpg, jpeg, png)
                                                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                                                if (!validTypes.includes(fileType)) {
                                                    imageError.textContent = 'Please upload an image of type JPG, JPEG, or PNG.';
                                                    imageError.style.display = 'block';
                                                    imageInput.value = ''; // Reset file input
                                                    return;
                                                }

                                                // Validate file size (max 10MB)
                                                if (fileSize > 10 * 1024 * 1024) { // 10MB in bytes
                                                    imageError.textContent = 'Maximum file size is 10MB.';
                                                    imageError.style.display = 'block';
                                                    imageInput.value = ''; // Reset file input
                                                    return;
                                                }

                                                // If all validations pass, hide error message
                                                imageError.style.display = 'none';
                                            }
                                        });
                                    });
                                </script>



                                <div class="flex justify-end">
                                    <button type="button" id="saveButton" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 flex items-center">
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
                                let copiesCount = 0;

                                // Handle input changes in the book_copies input field
                                document.getElementById("book_copies").addEventListener("input", function() {
                                    copiesCount = parseInt(this.value, 10) || 0; // Ensure a valid number
                                    updateAccessionFields();
                                });

                                // Handle the increment button click
                                document.getElementById("incrementBtn").addEventListener("click", function() {
                                    copiesCount++;
                                    document.getElementById("book_copies").value = copiesCount; // Update the input value
                                    updateAccessionFields(); // Update the accession number fields
                                });

                                // Handle the decrement button click
                                document.getElementById("decrementBtn").addEventListener("click", function() {
                                    if (copiesCount > 0) {
                                        copiesCount--;
                                        document.getElementById("book_copies").value = copiesCount; // Update the input value
                                        updateAccessionFields(); // Update the accession number fields
                                    }
                                });

                                // Function to update accession number fields based on copies count
                                function updateAccessionFields() {
                                    const accessionContainer = document.getElementById("accessionNumberContainer");
                                    accessionContainer.innerHTML = ''; // Clear existing fields

                                    for (let i = 1; i <= copiesCount; i++) {
                                        const accessionDiv = document.createElement("div");
                                        accessionDiv.classList.add("grid", "grid-cols-1", "sm:grid-cols-3", "items-center", "gap-4");

                                        const label = document.createElement("label");
                                        label.textContent = `ACCESSION NO ${i}:`;
                                        label.classList.add("text-left");

                                        const inputContainer = document.createElement("div");
                                        inputContainer.classList.add("col-span-2");

                                        const input = document.createElement("input");
                                        input.type = "text";
                                        input.id = `accession_no_${i}`;
                                        input.name = `accession_no_${i}`;
                                        input.placeholder = `Accession Number ${i}`;
                                        input.classList.add("border", "rounded", "px-3", "py-2", "w-full");

                                        // Add an error message container for this input
                                        const errorMessage = document.createElement("small");
                                        errorMessage.id = `accession_no_error_${i}`;
                                        errorMessage.classList.add("text-red-600");
                                        errorMessage.style.display = "none"; // Hidden by default
                                        errorMessage.textContent = "This Accession Number already exists. Please use a unique value.";

                                        // Add autocomplete functionality to the input field
                                        input.addEventListener("input", function() {
                                            const value = input.value.trim();
                                            if (value.length > 0) {
                                                fetch(`add_books_check_accession.php?term=${encodeURIComponent(value)}`)
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        const dataListId = `datalist_${i}`;
                                                        let dataList = document.getElementById(dataListId);

                                                        if (!dataList) {
                                                            dataList = document.createElement("datalist");
                                                            dataList.id = dataListId;
                                                            document.body.appendChild(dataList);
                                                            input.setAttribute("list", dataListId);
                                                        }

                                                        dataList.innerHTML = '';
                                                        data.forEach(item => {
                                                            const option = document.createElement("option");
                                                            option.value = item;
                                                            dataList.appendChild(option);
                                                        });
                                                    })
                                                    .catch(error => console.error("Autocomplete error:", error));
                                            }
                                        });

                                        // Add blur event to validate duplicates
                                        input.addEventListener("blur", function() {
                                            const value = input.value.trim();
                                            if (value.length > 0) {
                                                fetch(`add_books_check_accession.php?accession_no=${encodeURIComponent(value)}`)
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.isDuplicate) {
                                                            errorMessage.style.display = "block";
                                                            input.classList.add("border-red-600");
                                                        } else {
                                                            errorMessage.style.display = "none";
                                                            input.classList.remove("border-red-600");
                                                        }
                                                    })
                                                    .catch(error => console.error("Validation error:", error));
                                            } else {
                                                errorMessage.style.display = "none";
                                                input.classList.remove("border-red-600");
                                            }
                                        });

                                        inputContainer.appendChild(input);
                                        inputContainer.appendChild(errorMessage);

                                        accessionDiv.appendChild(label);
                                        accessionDiv.appendChild(inputContainer);
                                        accessionContainer.appendChild(accessionDiv);
                                    }
                                }
                            </script>








                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const checkbox = document.getElementById('checkbox_id');
                                    const categorySelect = document.getElementById('category');
                                    const addCategoryInput = document.getElementById('add_category');
                                    const saveButton = document.getElementById('saveButton'); // Save button
                                    const form = document.getElementById('categoryForm'); // Form element
                                    const checkboxBorrowable = document.getElementById('borrowable'); // Borrowable checkbox

                                    // Toggle category input fields based on checkbox
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

                                    // Add click event listener to the Save button
                                    saveButton.addEventListener('click', function() {
                                        let hasError = false;

                                        // Check for Call Number error
                                        const callNumberError = document.getElementById('call_number_error');
                                        if (callNumberError && getComputedStyle(callNumberError).display !== 'none') {
                                            hasError = true; // Error is visible
                                        }

                                        // Check for Accession Number errors
                                        const accessionErrors = document.querySelectorAll('[id^="accession_no_error_"]');
                                        accessionErrors.forEach((errorField) => {
                                            if (getComputedStyle(errorField).display !== 'none') {
                                                hasError = true; // Error is visible
                                            }
                                        });

                                        // Ensure a category is selected or added
                                        if (!categorySelect.value && !addCategoryInput.value.trim()) {
                                            alert('Please select a category or add a new category.');
                                            hasError = true;
                                        }

                                        // If any errors exist, stop submission and alert the user
                                        if (hasError) {
                                            alert('Please fix the errors before submitting the form.');
                                            return; // Stop further execution
                                        }

                                        // Prepare data for submission
                                        const formData = new FormData(form);

                                        // Add category data based on the checkbox state
                                        if (checkbox.checked) {
                                            formData.set('category', addCategoryInput.value.trim());
                                        } else {
                                            formData.set('category', categorySelect.value);
                                        }
                                        if (checkboxBorrowable.checked) {
                                            formData.set('borrowable', 'yes'); // If checked, send 'yes'
                                        } else {
                                            formData.set('borrowable', 'no'); // If not checked, send 'no'
                                        }

                                        let dataToSend = '';
                                        for (let [key, value] of formData.entries()) {
                                            dataToSend += `${key}: ${value}\n`;
                                        }
                                        alert(`Data to be sent:\n${dataToSend}`);


                                        // Send data to add_books_handle_category.php via POST
                                        fetch('add_books_handle_category.php', {
                                                method: 'POST',
                                                body: formData,
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    alert('Form submitted successfully.');
                                                    // Optionally redirect or clear the form here
                                                    form.reset();
                                                } else {
                                                    alert('Error: ' + (data.message || 'An error occurred while processing the form.'));
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                alert('An unexpected error occurred. Please try again later.');
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