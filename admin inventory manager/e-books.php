<?php
session_start();
if (!isset($_SESSION['logged_Admin_assistant']) || $_SESSION['logged_Admin_assistant'] !== true) {
    header('Location: ../index.php');

    exit;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'admin_header.php'; ?>

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


                        <li><a class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" href="#">All</a></li>
                        <br>
                        <li><a class="px-4 py-2 " href="add_e-books.php">Add E-Books</a></li>



                        <br>

                        <!-- <li><a class="px-4 py-2 " href="subject_for_replacement.php">Subject for Replacement</a></li> -->
                    </ul> <!-- Button beside the title -->


                </div>




                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
                        <!-- Search Input and Button -->
                        <div class="relative flex items-center">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="text" id="table-search-users" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-full md:w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for users">
                        </div>
                    </div>

                    <!-- Display Table Data -->
                    <div class="overflow-x-auto">
                        <div class="scrollable-table-container border border-gray-200 dark:border-gray-700">
                            <div class="container mx-auto px-4 py-6">
                                <ul id="tableData" class="flex flex-col space-y-4 ">
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
                            const searchInput = document.getElementById('table-search-users');
                            const tableDataContainer = document.getElementById('tableData');

                            let allRecords = []; // To store all fetched records
                            let filteredRecords = []; // To store filtered records
                            let currentPage = 1; // To track the current page
                            const recordsPerPage = 5; // Number of records per page

                            function loadTableData() {
                                fetch('e-books_fetch_data.php')
                                    .then(response => response.json())
                                    .then(data => {
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
                <br>
                <div class="flex-1 border-l-2 border-black p-4 h-full">
                    <h2 class="text-lg font-semibold mb-2">${record.title}</h2>
                    <span class="block text-base mb-2">by ${record.author}</span>
                    <span class="block text-sm text-gray-600 mb-2">Subject: ${record.subject}</span> <!-- Added subject -->
                </div>
                <div class="flex-shrink-0">
                    <a href="${record.link}" target="_blank" class="text-green-600 hover:underline">Read Online</a> <!-- Made "Read Online" clickable -->
                </div>
                <div class="flex-shrink-0">
                    <a href="${record.link}" target="_blank">
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

                                // Page numbers
                                for (let i = 1; i <= totalPages; i++) {
                                    const pageItem = document.createElement('li');
                                    pageItem.innerHTML = `
                        <a href="#" class="flex items-center justify-center px-4 h-10 leading-tight ${i === currentPage ? 'text-blue-600 border border-gray-300 bg-blue-50' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700'}">
                            ${i}
                        </a>`;
                                    pageItem.addEventListener('click', function(event) {
                                        event.preventDefault();
                                        currentPage = i;
                                        displayRecords(filteredRecords);
                                        setupPagination(filteredRecords.length);
                                    });
                                    paginationContainer.appendChild(pageItem);
                                }

                                // Next button
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

                            loadTableData(); // Load initial table data
                        });
                    </script>
                </div>




                <!-- Main Content Box -->




            </div>

        </div>
        </div>

    </main>

    <script src="./src/components/header.js"></script>

</body>

</html>