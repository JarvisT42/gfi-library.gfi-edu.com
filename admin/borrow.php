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

</head>
<style>
    .active-book-borrow {
        background-color: #f0f0f0;
        color: #000;
    }

    .active-request {
        background-color: #f0f0f0;
        color: #000;
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


                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-start space-x-4">
                    <label for="full_name" class="text-left">USER CATEGORY:&nbsp;&nbsp;&nbsp;</label>
                    <select id="role" name="role" class="col-span-2 border rounded px-3 py-2">
                        <option value="">Select Role</option>
                        <option value="Student">Student</option>
                        <option value="Faculty">Faculty</option>
                    </select>
                    <label for="full_name" class="text-left">FULL NAME:&nbsp;&nbsp;&nbsp;</label>
                    <input id="full_name" name="full_name" placeholder="Full Name" class="col-span-2 border rounded px-3 py-2" />

                    <label for="date" class="text-left">DUE DATE:</label>
                    <input type="date" id="date" name="date" class="border rounded px-3 py-2" />
                </div>



                <script>
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('date').setAttribute('min', today);
                </script>



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
                            </div> <input type="text" id="table-search-users" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-full md:w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for Title or Author">
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

                                    // Get the role value
                                    const role = document.getElementById("role").value;

                                    // Get the full name value
                                    const fullName = document.getElementById("full_name").value.trim();

                                    // Get the due date value
                                    const dueDate = document.getElementById("date").value;

                                    // Check if role is not selected
                                    if (!role) {
                                        alert("Please select a role.");
                                        return; // Stop the function if the role is not selected
                                    }

                                    // Check if the full name is empty
                                    if (!fullName) {
                                        alert("Please enter a full name.");
                                        return; // Stop the function if the full name is empty
                                    }

                                    // Check if the due date is empty
                                    if (!dueDate) {
                                        alert("Please enter a due date.");
                                        return; // Stop the function if the due date is empty
                                    }

                                    // Check if count is 0
                                    if (parseInt(bookBagCount) === 0) {
                                        alert("Book bag is empty");
                                    } else {
                                        // Redirect to book_bag.php with the role, full name, and due date as URL parameters
                                        window.location.href = "book_bag.php?role=" + encodeURIComponent(role) + "&full_name=" + encodeURIComponent(fullName) + "&due_date=" + encodeURIComponent(dueDate);
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


                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const button = document.getElementById('dropdownActionButton');
                            const menu = document.getElementById('dropdownAction');
                            const dropdownItems = document.querySelectorAll('#dropdownAction a');
                            const selectedField = document.getElementById('selectedField');
                            const tableDataContainer = document.getElementById('tableData');
                            const bookBagCountSpan = document.getElementById('bookBagCount');
                            const searchInput = document.getElementById('table-search-users');
                            const checkboxOption = document.getElementById('checkboxOption');

                            let allRecords = []; // To store all fetched records
                            let filteredRecords = []; // To store filtered records
                            let currentPage = 1; // To track the current page
                            const recordsPerPage = 5; // Number of records per page



                            function loadTableData(tableName) {

                                fetch(`fetch_table_data.php?table=${encodeURIComponent(tableName)}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        // Update book bag count
                                        bookBagCountSpan.textContent = data.bookBagCount;

                                        allRecords = data.data; // Store the fetched records
                                        filteredRecords = allRecords; // Initialize filtered records
                                        displayRecords(filteredRecords);
                                        setupPagination(filteredRecords.length);
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
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="text-sm text-gray-600">Published</div>
                        <div class="text-sm text-gray-600">${record.publicationDate}</div>
                        <div class="text-sm text-gray-600">Copies: ${record.copies}</div>
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
                                data-in-bag="${record.inBag}">
                                ${record.inBag ? '<span class="fa fa-minus"></span> Remove from Book Bag' : '<span class="fa fa-plus"></span> Add to Book Bag'}
                            </a>`
                    }
                </div>
                <div class="flex-shrink-0">
                    <a href="#">
                        <img src="${record.coverImage}" alt="Book Cover" class="w-28 h-40 border-2 border-gray-400 rounded-lg object-cover">
                    </a>
                </div>
            </div>
        </li>
    `).join('');
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
                                        copies: link.getAttribute('data-copies')
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


                            searchInput.addEventListener('input', function() {
                                const searchTerm = searchInput.value.toLowerCase();
                                filteredRecords = allRecords.filter(record => {
                                    const isAvailable = checkboxOption.checked ? record.copies > 1 : true;
                                    return (record.title.toLowerCase().includes(searchTerm) || record.author.toLowerCase().includes(searchTerm)) && isAvailable;
                                });

                                currentPage = 1; // Reset to first page
                                displayRecords(filteredRecords);
                                setupPagination(filteredRecords.length);
                            });

                            checkboxOption.addEventListener('change', function() {
                                const searchTerm = searchInput.value.toLowerCase();
                                filteredRecords = allRecords.filter(record => {
                                    const isAvailable = checkboxOption.checked ? record.copies > 1 : true;
                                    return (record.title.toLowerCase().includes(searchTerm) || record.author.toLowerCase().includes(searchTerm)) && isAvailable;
                                });

                                currentPage = 1; // Reset to first page
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
    <script>
        // Function to automatically show the dropdown if on book_request.php
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownRequest = document.getElementById('dropdown-request');

            // Open the dropdown menu for 'Request'
            dropdownRequest.classList.remove('hidden');
            dropdownRequest.classList.add('block'); // Make the dropdown visible

        });
    </script>

    <script src="./src/components/header.js"></script>

</body>

</html>