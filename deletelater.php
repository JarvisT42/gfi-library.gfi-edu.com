


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'admin_header.php'; ?>


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
                                        <div id="callNumberWarning" class="text-red-600 mt-1"></div>
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
                                    <div class="col-span-2 flex items-center gap-2">
                                        <!-- Minus Button -->
                                        <button id="decrementBtn" type="button" class="bg-blue-500 text-white p-2 rounded">
                                            <i class="fas fa-minus"></i> <!-- Font Awesome Minus Icon -->
                                        </button>
                                        <input id="book_copies" name="book_copies" type="number" class="col-span-2 border rounded px-3 py-2" value="0" />
                                        <!-- Plus Button -->
                                        <button id="incrementBtn" type="button" class="bg-blue-500 text-white p-2 rounded">
                                            <i class="fas fa-plus"></i> <!-- Font Awesome Plus Icon -->
                                        </button>

                                        <!-- Display Book Copies (Read-only) -->
                                      
                                    </div>
                                </div>

                                <div id="accessionNumberContainer"></div>



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
                                    <label for="borrowable" class="text-left">SET AS BORROWABLE:</label>
                                    <input
                                        id="borrowable"
                                        name="borrowable"
                                        type="checkbox"
                                        class="col-span-2 border rounded px-3 py-2" />
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






                        </div>
                    </div>


                </div>



            </div>

        </div>
        </div>

    </main>


</body>

</html>