<?php
session_start();
if (!isset($_SESSION['logged_Admin_assistant']) || $_SESSION['logged_Admin_assistant'] !== true) {
    header('Location: ../index.php');

    exit;
}



?>
<!DOCTYPE html>
<html lang="en">
<?php include 'admin_header.php'; ?>

<style>
    .preview-image img {
        outline: none;
    }

    .preview-image:focus,
    .preview-image img:focus {
        outline: none;
    }
</style>

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
                        <li><a class="px-4 py-2 " href="add_books.php">Add Books</a></li>
                        <br>
                        <li><a class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" href="edit_records.php">Edit Records</a></li>

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
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
                        <div class="flex items-center space-x-4">
                            <!-- Dropdown Button -->

                            <?php
                            // Database connection
                            require '../connection2.php';

                            if ($conn2->connect_error) {
                                die("Connection failed: " . $conn2->connect_error);
                            }

                            // Query to fetch all table names
                            $sql = "SHOW TABLES FROM dnllaaww_gfi_library_books_inventory";
                            $result = $conn2->query($sql);
                            ?>

                            <div class="relative inline-block text-left">
                                <!-- Dropdown button -->
                                <button id="dropdownActionButton" type="button" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                    <span id="selectedField">All fields</span>
                                    <svg class="w-2.5 h-2.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg>
                                </button>

                                <!-- Dropdown menu -->
                                <div id="dropdownAction" class="z-10 hidden absolute mt-2 w-44 bg-white divide-y divide-gray-100 rounded-lg shadow-lg dark:bg-gray-700 dark:divide-gray-600">
                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownActionButton">
                                        <!-- Default "All fields" option -->
                                        <li>
                                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white" data-table="All fields">All fields</a>
                                        </li>
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_array()) {
                                                $tableName = htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8');
                                                // Exclude the 'e-books' table
                                                if ($tableName !== 'e-books') {
                                                    echo '<li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white" data-table="' . $tableName . '">' . $tableName . '</a></li>';
                                                }
                                            }
                                        } else {
                                            echo '<li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">No tables found</a></li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">

                                <!-- Add this dropdown inside your HTML where appropriate, such as near the top of your table/list -->
                                <select id="sortDropdown" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                    <option value="relevance">Sort by Relevance</option>

                                    <option value="title">Sort by Title</option>
                                    <option value="author">Sort by Author</option>
                                </select>
                            </div>


                            <!-- Checkbox -->
                            <!--<div class="flex items-center space-x-2">-->
                            <!--    <input type="checkbox" id="checkboxOption" name="checkboxGroup" class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded-lg focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-500 transition-transform transform hover:scale-105">-->
                            <!--    <label for="checkboxOption" class="text-sm text-gray-900 dark:text-gray-300">Available</label>-->
                            <!--</div>-->
                        </div>
                        <!-- Search Input and Button -->
                        <div class="relative flex items-center">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="text" id="table-search-users" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-full md:w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for Title or Author" />










                        </div>

                    </div>
                    <!-- Display Table Data -->
                    <div class="overflow-x-auto">
                        <div class="scrollable-table-container border border-gray-200 dark:border-gray-700">
                            <div class="container mx-auto px-4 py-6">
                                <ul id="tableData" class="flex flex-col space-y-4">
                                    <!-- Table data will be inserted here -->
                                </ul>
                            </div>


                        </div>



                    </div>



                    <br>

                    <nav aria-label="Page navigation example" class="flex items-center justify-center mt-4">
                        <ul class="inline-flex -space-x-px text-base h-10">
                            <li>
                                <a href="#" class="flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Previous</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">1</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">2</a>
                            </li>
                            <li>
                                <a href="#" aria-current="page" class="flex items-center justify-center px-4 h-10 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">3</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">4</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">5</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Next</a>
                            </li>
                        </ul>
                    </nav>

                    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden flex items-center justify-center z-50">
                        <div class="relative">
                            <!-- Close button -->
                            <button id="closeModal" class="absolute top-2 right-2 text-white text-2xl font-bold">&times;</button>
                            <!-- Image preview -->
                            <img id="modalImage" src="" alt="Image Preview" class="max-w-full max-h-screen rounded-lg shadow-lg">
                        </div>
                    </div>


                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const button = document.getElementById('dropdownActionButton');
                            const menu = document.getElementById('dropdownAction');
                            const dropdownItems = document.querySelectorAll('#dropdownAction a');
                            const selectedField = document.getElementById('selectedField');
                            const tableDataContainer = document.getElementById('tableData');
                            const bookBagCountSpan = document.getElementById('bookBagCount');
                            const searchInput = document.getElementById('table-search-users');
                            const sortDropdown = document.getElementById('sortDropdown');
                            const imageModal = document.getElementById('imageModal');
                            const modalImage = document.getElementById('modalImage');
                            const closeModal = document.getElementById('closeModal');

                            let allRecords = []; // To store all fetched records
                            let filteredRecords = []; // To store filtered records
                            let currentPage = 1; // To track the current page
                            const recordsPerPage = 10; // Number of records per page

                            // Sorting and Filter Logic
                            function handleSortChange() {
                                const sortBy = sortDropdown.value; // Get the selected sort option
                                if (!allRecords.length) {
                                    console.error('No records to sort!');
                                    return;
                                }

                                filteredRecords = [...allRecords]; // Start fresh with all records
                                if (sortBy === 'title') {
                                    filteredRecords.sort((a, b) => a.title.localeCompare(b.title)); // Sort by title
                                } else if (sortBy === 'author') {
                                    filteredRecords.sort((a, b) => a.author.localeCompare(b.author)); // Sort by author
                                }
                                displayRecords(filteredRecords); // Update display
                                setupPagination(filteredRecords.length); // Update pagination
                            }

                            // Load and Display Data
                            function loadTableData(tableName) {
                                fetch(`fetch_table_data.php?table=${encodeURIComponent(tableName)}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        allRecords = data.data || []; // Store fetched records
                                        filteredRecords = allRecords; // Initialize filtered records
                                        displayRecords(filteredRecords);
                                        setupPagination(filteredRecords.length);
                                        sortDropdown.value = 'relevance'; // Default sorting
                                        handleSortChange(); // Apply sorting
                                    });
                            }

                            function displayRecords(records) {
                                const startIndex = (currentPage - 1) * recordsPerPage;
                                const endIndex = startIndex + recordsPerPage;
                                const paginatedRecords = records.slice(startIndex, endIndex);

                                tableDataContainer.innerHTML = paginatedRecords.map((record, index) => `
            <li class="bg-gray-200 p-4 flex items-center border-b-2 border-black">
                <div class="flex flex-row items-start w-full space-x-6 overflow-x-auto">
                    <div class="flex-none w-12">
                        <div class="text-lg font-semibold text-gray-800">${startIndex + index + 1}</div>
                    </div>
                    <div class="flex-1 border-l-2 border-black p-4">
                        <a href="edit_book.php?id=${record.id}&table=${record.table}" class="block">
                            <h2 class="text-lg font-semibold mb-2">${record.title}</h2>
                            <span class="block text-base mb-2">by ${record.author}</span>
                            <div class="flex items-center space-x-2 mb-2">
                                <div class="text-sm text-gray-600">Call Number: ${record.callNumber}</div>
                            </div>
                            <div class="flex items-center space-x-2 mb-2">
                                <div class="text-sm text-gray-600">Published</div>
                                <div class="text-sm text-gray-600">${record.publicationDate}</div>
                                <div class="text-sm text-gray-600">copies ${record.copies}</div>
                            </div>
                            <div class="bg-blue-200 p-2 rounded-lg shadow-md text-left mt-auto inline-block border border-blue-300">
                                ${record.table}
                            </div>
                        </a>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="#" class="preview-image">
                            <img src="${record.imagePath}" alt="Book Cover" class="w-28 h-40 border-2 border-gray-400 rounded-lg object-cover">
                        </a>
                    </div>
                </div>
            </li>
        `).join('');

                                // Attach click event to each image with the preview-image class
                                document.querySelectorAll('.preview-image img').forEach(image => {
                                    image.addEventListener('click', function(event) {
                                        event.preventDefault();
                                        modalImage.src = this.src; // Set the clicked image as the modal image
                                        imageModal.classList.remove('hidden'); // Show the modal
                                    });
                                });
                            }

                            // Pagination
                            function setupPagination(totalRecords) {
                                const totalPages = Math.ceil(totalRecords / recordsPerPage);
                                const paginationContainer = document.querySelector('nav ul');
                                paginationContainer.innerHTML = '';

                                const prevButton = document.createElement('li');
                                prevButton.innerHTML = `<a href="#" class="flex items-center justify-center px-4 h-10 leading-tight ${currentPage === 1 ? 'text-gray-300' : 'text-gray-500'} bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>`;
                                prevButton.addEventListener('click', function(event) {
                                    event.preventDefault();
                                    if (currentPage > 1) {
                                        currentPage--;
                                        displayRecords(filteredRecords);
                                        setupPagination(filteredRecords.length);
                                    }
                                });
                                paginationContainer.appendChild(prevButton);

                                for (let i = 1; i <= totalPages; i++) {
                                    const pageItem = document.createElement('li');
                                    pageItem.innerHTML = `<a href="#" class="flex items-center justify-center px-4 h-10 leading-tight ${i === currentPage ? 'text-blue-600 border border-gray-300 bg-blue-50' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700'}">${i}</a>`;
                                    pageItem.addEventListener('click', function(event) {
                                        event.preventDefault();
                                        currentPage = i;
                                        displayRecords(filteredRecords);
                                        setupPagination(filteredRecords.length);
                                    });
                                    paginationContainer.appendChild(pageItem);
                                }

                                const nextButton = document.createElement('li');
                                nextButton.innerHTML = `<a href="#" class="flex items-center justify-center px-4 h-10 leading-tight ${currentPage === totalPages ? 'text-gray-300' : 'text-gray-500'} bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>`;
                                nextButton.addEventListener('click', function(event) {
                                    event.preventDefault();
                                    if (currentPage < totalPages) {
                                        currentPage++;
                                        displayRecords(filteredRecords);
                                        setupPagination(filteredRecords.length);
                                    }
                                });
                                paginationContainer.appendChild(nextButton);
                            }

                            // Dropdown Menu
                            button.addEventListener('click', function() {
                                menu.classList.toggle('hidden');
                            });

                            dropdownItems.forEach(item => {
                                item.addEventListener('click', function(event) {
                                    event.preventDefault();
                                    const tableName = this.getAttribute('data-table');
                                    selectedField.textContent = tableName;
                                    menu.classList.add('hidden');
                                    loadTableData(tableName);
                                });
                            });

                            // Search Input
                            searchInput.addEventListener('input', function() {
                                const searchTerm = searchInput.value.toLowerCase();
                                filteredRecords = allRecords.filter(record =>
                                    record.title.toLowerCase().includes(searchTerm) || record.author.toLowerCase().includes(searchTerm)
                                );
                                currentPage = 1;
                                displayRecords(filteredRecords);
                                setupPagination(filteredRecords.length);
                            });

                            // Modal Close Functionality
                            closeModal.addEventListener('click', () => {
                                imageModal.classList.add('hidden');
                                modalImage.src = '';
                            });

                            imageModal.addEventListener('click', (event) => {
                                if (event.target === imageModal) {
                                    imageModal.classList.add('hidden');
                                    modalImage.src = '';
                                }
                            });

                            // Initialize Table Data
                            loadTableData('All fields');

                            // Sort Dropdown Event Listener
                            sortDropdown.addEventListener('change', handleSortChange);
                        });
                    </script>




                </div>










            </div>

        </div>
        </div>

    </main>

    <script src="./src/components/header.js"></script>

</body>

</html>