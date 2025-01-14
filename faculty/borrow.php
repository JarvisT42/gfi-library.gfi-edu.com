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
    <link rel="stylesheet" href="borrow.css">
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>

</head>
<style>
    .preview-image img {
        outline: none;
        /* Remove outline for images */
    }

    .preview-image:focus,
    .preview-image img:focus {
        outline: none;
        /* Remove outline when focused */
    }
</style>

<body>
    <?php include './src/components/sidebar.php'; ?>

    <main id="content" class="">


        <div class="p-4 sm:ml-64">

            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">

                <!-- Title Box -->
                <!-- Title and Button Box -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <h1 class="text-3xl font-semibold">Borrow</h1> <!-- Adjusted text size -->
                    <!-- Button beside the title -->
                </div>

                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                    The Borrow Page is your gateway to accessing and managing book loans efficiently. On this page, you can search for and borrow books from our collection with ease. Simply browse or search for the titles you wish to borrow, select your preferred books, and follow the streamlined borrowing process. The page also provides a clear overview of the available books and their details.
                </div>

                <!-- Main Content Box -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 min-h-screen">

                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
                        <div class="flex items-center space-x-4">
                            <!-- Dropdown Button -->


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

                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="checkboxOption" name="checkboxGroup" class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded-lg focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-500 transition-transform transform hover:scale-105">
                                <label for="checkboxOption" class="text-sm text-gray-900 dark:text-gray-300">Available</label>
                            </div>

                        </div>
                        <!-- Search Input and Button -->
                        <div class="relative flex items-center">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 19l-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                id="table-search-users"
                                class="block w-full pl-10 pr-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:focus:ring-gray-700"
                                placeholder="Search for Title or Author"
                                aria-label="Search for Title or Author">

                            <button type="button" id="bookBagButton" class="relative ml-2 inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 whitespace-nowrap">
                                Book Bag
                                <span id="bookBagCount" class="absolute -top-2 -right-2 inline-flex items-center justify-center w-6 h-6 text-xs font-semibold text-white bg-red-500 rounded-full">
                                    0
                                </span>
                            </button>

                            <script>
                                // JavaScript function to handle button click
                                document.getElementById("bookBagButton").addEventListener("click", function() {
                                    // Get the book bag count
                                    const bookBagCount = document.getElementById("bookBagCount").innerText;

                                    // Check if count is 0
                                    if (parseInt(bookBagCount) === 0) {
                                        alert("Book bag is empty");
                                    } else {
                                        // Redirect to book_php if count is greater than 0s
                                        window.location.href = "book_bag.php";
                                    }
                                });
                            </script>








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

                            const loadingSpinner = document.getElementById('loadingSpinner'); // Loading spinner element

                            let currentTable = 'All fields'; // Default table selection


                            const selectedField = document.getElementById('selectedField');
                            const tableDataContainer = document.getElementById('tableData');
                            const bookBagCountSpan = document.getElementById('bookBagCount');
                            const searchInput = document.getElementById('table-search-users');
                            const checkboxOption = document.getElementById('checkboxOption');

                            let allRecords = []; // To store all fetched records
                            let filteredRecords = []; // To store filtered records
                            let currentPage = 1; // To track the current page
                            const recordsPerPage = 10; // Number of records per page

                            const imageModal = document.getElementById('imageModal');
                            const modalImage = document.getElementById('modalImage');
                            const closeModal = document.getElementById('closeModal');


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




                            document.getElementById('sortDropdown').addEventListener('change', handleSortChange);


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
                                                <h2 class="text-lg font-semibold mb-2">${record.title}</h2>
                                                <span class="block text-base mb-2">by ${record.author}</span>
                                                
                                                <!-- Added Volume Info -->
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
                                                ${record.availableToBorrow === 'No'
                                                    ? `<span class="text-red-600">Not Available</span>`
                                                    : record.currentlyBorrowed
                                                        ? `<span class="text-yellow-600">Currently Borrowed</span>`
                                                        : `<a href="#" class="${record.inBag ? 'text-red-600' : 'text-green-600'} hover:underline book-bag-toggle"
                                                            data-id="${record.id}"
                                                            data-title="${record.title}"
                                                            data-author="${record.author}"
                                                            data-publication-date="${record.publicationDate}"
                                                            data-table="${record.table}"
                                                            data-cover-image="${record.coverImage}"
                                                            data-copies="${record.copies}"
                                                            data-in-bag="${record.inBag}"
                                                            data-volume="${record.volume}" 
                                                            data-edition="${record.edition}">
                                                            ${record.inBag ? '<span class="fa fa-minus"></span> Remove from Book Bag' : '<span class="fa fa-plus"></span> Add to Book Bag'}
                                                        </a>`
                                                }
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

                            loadTableData('All fields');





                            tableDataContainer.addEventListener('click', function(event) {
                                if (event.target.classList.contains('book-bag-toggle')) {
                                    event.preventDefault();
                                    const link = event.target;
                                    const inBag = link.getAttribute('data-in-bag') === 'true';

                                    const bookData = {
                                        id: link.getAttribute('data-id'),
                                        title: link.getAttribute('data-title'),
                                        author: link.getAttribute('data-author'),
                                        publicationDate: link.getAttribute('data-publication-date'),
                                        table: link.getAttribute('data-table'),
                                        coverImage: link.getAttribute('data-cover-image'),
                                        copies: link.getAttribute('data-copies'),
                                        volume: link.getAttribute('data-volume'), // Add volume here
                                        edition: link.getAttribute('data-edition') // Add edition here
                                    };

                                    const currentBookBagCount = parseInt(bookBagCountSpan.textContent);

                                    if (!inBag) {
                                        if (currentBookBagCount >= 3) {
                                            alert('You can only borrow a maximum of 3 books.');
                                            return;
                                        }

                                        // In the fetch response for adding a book
                                        fetch('add_to_book_bag.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json'
                                                },
                                                body: JSON.stringify(bookData)
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.status === 'success') {
                                                    link.textContent = 'Remove from Book Bag';
                                                    link.classList.remove('text-green-600');
                                                    link.classList.add('text-red-600');
                                                    link.setAttribute('data-in-bag', 'true');
                                                    bookBagCountSpan.textContent = parseInt(bookBagCountSpan.textContent) + 1;
                                                } else {
                                                    alert(data.message); // Show appropriate message if the limit is reached
                                                }
                                            });


                                    } else {
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
                                                    link.textContent = 'Add to Book Bag';
                                                    link.classList.remove('text-red-600');
                                                    link.classList.add('text-green-600');
                                                    link.setAttribute('data-in-bag', 'false');
                                                    bookBagCountSpan.textContent = currentBookBagCount - 1;
                                                } else {
                                                    alert('Failed to remove book from the bag.');
                                                }
                                            });
                                    }
                                }
                            });




                            checkboxOption.addEventListener('change', function() {
                                // Reapply filters when the checkbox is toggled
                                filteredRecords = applyFilters(allRecords);
                                currentPage = 1; // Reset to the first page
                                displayRecords(filteredRecords);
                                setupPagination(filteredRecords.length);
                            });



                            checkboxOption.addEventListener('change', function() {
                                const searchTerm = searchInput.value.toLowerCase();

                                // Filter records based on availability and search term
                                filteredRecords = allRecords.filter(record => {
                                    // Determine availability (update logic if needed)
                                    const isAvailable = checkboxOption.checked ? record.availableToBorrow === 'yes' : true;

                                    // Match search term in title or author
                                    return (
                                        (record.title.toLowerCase().includes(searchTerm) ||
                                            record.author.toLowerCase().includes(searchTerm)) &&
                                        isAvailable
                                    );
                                });

                                // Reset to first page and display filtered results
                                currentPage = 1;
                                displayRecords(filteredRecords);
                                setupPagination(filteredRecords.length);
                            });
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