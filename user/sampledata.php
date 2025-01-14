<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Page</title>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        #loadingIcon {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        #loadingIcon .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.2);
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-semibold mb-6">Books Page</h1>

        <!-- Dropdown for selecting table -->
        <div class="flex items-center mb-4">
            <select id="tableDropdown" class="border border-gray-300 rounded px-4 py-2 mr-4">
                <option value="All fields">All fields</option>
            </select>

            <input id="searchBar" type="text" placeholder="Search by title or author"
                class="border border-gray-300 rounded px-4 py-2 w-full">
        </div>

        <!-- Loading Spinner -->
        <div id="loadingIcon" class="hidden fixed top-0 left-0 w-full h-full flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
            <div class="spinner"></div>
        </div>

        <!-- Table for displaying data -->
        <ul id="tableData" class="space-y-4"></ul>

        <!-- Pagination -->
        <div id="pagination" class="flex justify-center mt-6"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableDropdown = document.getElementById('tableDropdown');
            const searchBar = document.getElementById('searchBar');
            const tableDataContainer = document.getElementById('tableData');
            const paginationContainer = document.getElementById('pagination');
            const loadingIcon = document.getElementById('loadingIcon');

            let allRecords = [];
            let filteredRecords = [];
            let currentPage = 1;
            const recordsPerPage = 10;

            // Show the loading spinner
            function showLoading() {
                loadingIcon.classList.remove('hidden');
            }

            // Hide the loading spinner
            function hideLoading() {
                loadingIcon.classList.add('hidden');
            }

            // Fetch data from the server
            async function fetchTableData(tableName = 'All fields', page = 1, searchQuery = '') {
                showLoading();

                try {
                    const response = await fetch(`sampledata2.php?table=${encodeURIComponent(tableName)}&page=${page}&search=${encodeURIComponent(searchQuery)}`);
                    const data = await response.json();

                    if (data.error) {
                        console.error(data.error);
                        return;
                    }

                    allRecords = data.data || [];
                    filteredRecords = allRecords;
                    renderTable(filteredRecords);
                    setupPagination(data.totalRecords);
                } catch (error) {
                    console.error('Error fetching data:', error);
                } finally {
                    hideLoading();
                }
            }

            // Render table data
            function renderTable(records) {
                tableDataContainer.innerHTML = records.map((record) => `
                    <li class="bg-gray-100 p-4 rounded shadow">
                        <h2 class="text-xl font-semibold">${record.title}</h2>
                        <p>Author: ${record.author}</p>
                        <p>Published: ${record.publicationDate}</p>
                        <p>Copies: ${record.copies}</p>
                    </li>
                `).join('');
            }

            // Setup pagination
            function setupPagination(totalRecords) {
    const totalPages = Math.ceil(totalRecords / recordsPerPage);
    paginationContainer.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement('button');
        pageButton.textContent = i;
        pageButton.classList.add('px-4', 'py-2', 'border', 'rounded', 'mx-1');
        if (i === currentPage) pageButton.classList.add('bg-blue-500', 'text-white');

        pageButton.addEventListener('click', () => {
            currentPage = i;
            fetchTableData(tableDropdown.value, currentPage, searchBar.value);
        });

        paginationContainer.appendChild(pageButton);
    }
}


            // Event listeners for dropdown and search bar
            tableDropdown.addEventListener('change', () => {
                currentPage = 1;
                fetchTableData(tableDropdown.value, currentPage, searchBar.value);
            });

            searchBar.addEventListener('input', () => {
                currentPage = 1;
                fetchTableData(tableDropdown.value, currentPage, searchBar.value);
            });

            // Initialize data
            fetchTableData();
        });
    </script>
</body>

</html>
