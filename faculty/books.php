<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
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
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <style>
        .active-books_first {
            background-color: #f0f0f0;
            color: #000;
        }

        .preview-image img {
            outline: none;
        }

        .preview-image:focus,
        .preview-image img:focus {
            outline: none;
        }
    </style>
</head>

<body>
    <?php include './src/components/sidebar.php'; ?>
    <main id="content" class="">
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 min-h-screen">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <h1 class="text-3xl font-semibold">Books</h1>
                </div>
                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                    The Books Page allows users to view the books available in our collection. On this page, you can search for specific titles, explore available books, and access detailed information about each one.
                </div>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 min-h-screen">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
                        <div class="flex items-center space-x-4">


                            <div class="flex items-center space-x-2">
                                <!-- Dropdown menu for sorting -->
                                <select id="dropdownAction" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                    <option value="All fields">All fields</option>
                                    <?php
                                    require '../connection2.php';
                                    if ($conn2->connect_error) {
                                        die("Connection failed: " . $conn2->connect_error);
                                    }
                                    $sql = "SHOW TABLES FROM dnllaaww_gfi_library_books_inventory";
                                    $result = $conn2->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_array()) {
                                            $tableName = htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8');
                                            if ($tableName !== 'e-books') {
                                                echo '<option value="' . $tableName . '">' . $tableName . '</option>';
                                            }
                                        }
                                    } else {
                                        echo '<option value="" disabled>No tables found</option>';
                                    }
                                    ?>
                                </select>

                            </div>



                            <div class="flex items-center space-x-2">

                                <!-- Add this dropdown inside your HTML where appropriate, such as near the top of your table/list -->
                                <select id="sortDropdown" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                    <option value="relevance">Sort by Relevance</option>

                                    <option value="title">Sort by Title</option>
                                    <option value="author">Sort by Author</option>
                                </select>
                            </div>

                        </div>
                        <!-- Search Input and Button -->
                        <div class="relative flex items-center">
                            <!-- Search Icon -->
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 19l-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>

                            <!-- Input Field -->
                            <input
                                type="text"
                                id="table-search-users"
                                class="block w-full pl-10 pr-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:focus:ring-gray-700"
                                placeholder="Search for Title or Author"
                                aria-label="Search for Title or Author">
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
                    <div id="loadingSpinner" class="hidden fixed inset-0 flex items-center justify-center bg-gray-100 bg-opacity-75">
                        <div class="spinner-border animate-spin inline-block w-12 h-12 border-4 rounded-full text-blue-600"></div>
                    </div>
                    <style>
                        .spinner-border {
                            border-top-color: transparent;
                            border-right-color: #3498db;
                            border-bottom-color: #3498db;
                            border-left-color: #3498db;
                        }
                    </style>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const dropdownSelect = document.getElementById('dropdownAction'); // Dropdown for selecting table
                            const searchInput = document.getElementById('table-search-users'); // Search input for filtering
                            const tableDataContainer = document.getElementById('tableData'); // Container to display table data
                            const loadingSpinner = document.getElementById('loadingSpinner'); // Loading spinner element
                            let allRecords = []; // Store all fetched records
                            let filteredRecords = []; // Store filtered records
                            let currentTable = 'All fields'; // Default table selection
                            let currentPage = 1; // Current pagination page
                            const recordsPerPage = 10; // Records to display per page

                            // Load initial table data
                            loadTableData(currentTable);

                            // Handle table selection change
                            dropdownSelect.addEventListener('change', function() {
                                currentTable = this.value; // Get the selected table
                                currentPage = 1; // Reset to the first page
                                applyFiltersAndDisplay(); // Apply filters and update display
                            });

                            // Filter records based on search input
                            searchInput.addEventListener('input', function() {
                                currentPage = 1; // Reset to the first page
                                applyFiltersAndDisplay(); // Apply filters and update display
                            });

                            function handleSortChange() {
                                const sortBy = document.getElementById('sortDropdown').value;

                                // Apply filters first (ensure filters are applied before sorting)
                                filteredRecords = applyFilters(allRecords); // Apply filters before sorting

                                // Sort the records based on the selected option (title, author, or relevance)
                                if (sortBy === 'title') {
                                    filteredRecords.sort((a, b) => a.title.localeCompare(b.title)); // Sort by title
                                } else if (sortBy === 'author') {
                                    filteredRecords.sort((a, b) => a.author.localeCompare(b.author)); // Sort by author
                                } else if (sortBy === 'relevance') {
                                    // For relevance, no sorting is needed, just apply filters
                                    // This will keep the records in their current order, unaffected by sorting
                                    // No sorting function is applied for relevance
                                }

                                // Re-render the table with the filtered and sorted records
                                displayRecords(filteredRecords);
                            }

                            // Call handleSortChange when the dropdown changes
                            document.getElementById('sortDropdown').addEventListener('change', handleSortChange);

                            // On page load, ensure that the default sort (Relevance) is selected and applied
                            document.addEventListener('DOMContentLoaded', function() {
                                document.getElementById('sortDropdown').value = 'relevance';
                                handleSortChange(); // Apply relevance (no sorting) when the page loads
                            });


                            // Function to load data from the server
                            function loadTableData(tableName) {
                                loadingSpinner.classList.remove('hidden'); // Show loading spinner

                                fetch(`fetch_table_data.php?table=${encodeURIComponent(tableName)}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        loadingSpinner.classList.add('hidden'); // Hide loading spinner

                                        allRecords = data.data; // Store fetched records
                                        applyFiltersAndDisplay(); // Apply filters and display records
                                    })
                                    .catch(error => {
                                        console.error('Error fetching data:', error);
                                        loadingSpinner.classList.add('hidden'); // Hide spinner on error
                                    });
                            }

                            // Function to apply filters and display records
                            function applyFiltersAndDisplay() {
                                filteredRecords = applyFilters(allRecords); // Apply filters to all records
                                displayRecords(filteredRecords); // Display the filtered records
                                setupPagination(filteredRecords.length); // Setup pagination for filtered records
                            }

                            // Filter records based on the selected table and search input
                            function applyFilters(records) {
                                const searchTerm = searchInput.value.toLowerCase(); // Get search term

                                // Filter by table and search term
                                return records.filter(record => {
                                    const matchesTable = currentTable === 'All fields' || record.table === currentTable;
                                    const matchesSearch = record.title.toLowerCase().includes(searchTerm) || record.author.toLowerCase().includes(searchTerm);
                                    return matchesTable && matchesSearch; // Both conditions must be true
                                });
                            }

                            // Display records with pagination
                            function displayRecords(records) {
                                const startIndex = (currentPage - 1) * recordsPerPage;
                                const paginatedRecords = records.slice(startIndex, startIndex + recordsPerPage);

                                tableDataContainer.innerHTML = paginatedRecords.map((record, index) => `
            <li class="bg-gray-200 p-4 flex items-center border-b-2 border-black">
                <div class="flex flex-row items-start w-full space-x-6 overflow-x-auto">
                    <div class="flex-none w-12">
                        <div class="text-lg font-semibold text-gray-800">${startIndex + index + 1}</div>
                    </div>
                    <div class="flex-1 border-l-2 border-black p-4">
                        <h2 class="text-lg font-semibold mb-2">${record.title}</h2>
                        <span class="block text-base mb-2">by ${record.author}</span>
                        ${record.volume ? `<span class="block text-sm text-gray-600 mb-2">Volume: ${record.volume}</span>` : ''}
                        ${record.edition ? `<span class="block text-sm text-gray-600 mb-2">Edition: ${record.edition}</span>` : ''}
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="text-sm text-gray-600">Published</div>
                            <div class="text-sm text-gray-600">${record.publicationDate}</div>
                            <div class="text-sm text-gray-600">copies ${record.copies}</div>
                        </div>
                        <div class="bg-blue-200 p-2 rounded-lg shadow-md text-left mt-auto inline-block border border-blue-300">
                            ${record.table}
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="#" class="preview-image">
                            <img src="${record.coverImage}" alt="Book Cover" class="w-28 h-40 border-2 border-gray-400 rounded-lg object-cover">
                        </a>
                    </div>
                </div>
            </li>
        `).join('');
                                // Attach click event to each image with the preview-image class
                                document.querySelectorAll('.preview-image img').forEach(image => {
                                    image.addEventListener('click', function(event) {
                                        event.preventDefault();
                                        modalImage.src = this.src; // Set the clicked image as sthe modal image
                                        imageModal.classList.remove('hidden'); // Show the modal
                                    });
                                });
                            }
                            // Close modal when the close button is clicked
                            closeModal.addEventListener('click', () => {
                                imageModal.classList.add('hidden');
                                modalImage.src = "";
                            });

                            // Close modal when clicking outside the image area
                            imageModal.addEventListener('click', (event) => {
                                if (event.target === imageModal) {
                                    imageModal.classList.add('hidden');
                                    modalImage.src = "";
                                }
                            });

                            // Setup pagination
                            function setupPagination(totalRecords) {
                                const totalPages = Math.ceil(totalRecords / recordsPerPage);
                                const paginationContainer = document.querySelector('nav ul');
                                paginationContainer.innerHTML = '';

                                // Add pagination logic here, similar to your original code...
                            }

                            function setupPagination(totalRecords) {
                                const totalPages = Math.ceil(totalRecords / recordsPerPage);
                                const paginationContainer = document.querySelector('nav ul');
                                paginationContainer.innerHTML = '';

                                // Previous button
                                const prevButton = document.createElement('li');
                                prevButton.innerHTML = `<a href="#" class="flex items-center justify-center px-4 h-10 leading-tight ${currentPage === 1 ? 'text-gray-300' : 'text-gray-500'} bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700" ${currentPage === 1 ? 'disabled' : ''}>Previous</a>`;
                                prevButton.addEventListener('click', function(event) {
                                    event.preventDefault();
                                    if (currentPage > 1) {
                                        currentPage--;
                                        displayRecords(filteredRecords);
                                        setupPagination(filteredRecords.length);
                                    }
                                });
                                paginationContainer.appendChild(prevButton);

                                // Page numbers
                                const pageNumbers = [];
                                for (let i = 1; i <= totalPages; i++) {
                                    if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                                        pageNumbers.push(i);
                                    } else if (pageNumbers[pageNumbers.length - 1] !== '...' && (i === 2 || i === totalPages - 1)) {
                                        pageNumbers.push('...');
                                    }
                                }

                                // Render the page numbers
                                pageNumbers.forEach(page => {
                                    const pageItem = document.createElement('li');
                                    if (page === '...') {
                                        pageItem.innerHTML = `<span class="flex items-center justify-center px-4 h-10">...</span>`;
                                    } else {
                                        pageItem.innerHTML = `
                        <a href="#" class="flex items-center justify-center px-4 h-10 leading-tight ${page === currentPage ? 'text-blue-600 border border-gray-300 bg-blue-50' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700'}">
                            ${page}
                        </a>
                    `;
                                        pageItem.addEventListener('click', function(event) {
                                            event.preventDefault();
                                            currentPage = page;
                                            displayRecords(filteredRecords);
                                            setupPagination(filteredRecords.length);
                                        });
                                    }
                                    paginationContainer.appendChild(pageItem);
                                });

                                // Next button
                                const nextButton = document.createElement('li');
                                nextButton.innerHTML = `<a href="#" class="flex items-center justify-center px-4 h-10 leading-tight ${currentPage === totalPages ? 'text-gray-300' : 'text-gray-500'} bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700" ${currentPage === totalPages ? 'disabled' : ''}>Next</a>`;
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