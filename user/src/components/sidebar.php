<button data-drawer-target="separator-sidebar" data-drawer-toggle="separator-sidebar" aria-controls="separator-sidebar" type="button" class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
    <span class="sr-only">Open sidebar</span>
    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
    </svg>
</button>
<aside id="separator-sidebar" class="fixed  top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 flex flex-col" aria-label="Sidebar">
    <div class="flex-1 px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800">
        <div class="flex items-center p-2 mb-4 mt-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
            <img class="w-12 h-12 rounded-full" src="https://via.placeholder.com/100" alt="Profile Picture">
            <div class="ms-4">
                <p class="text-gray-900 dark:text-white text-lg font-medium">
                    <?php echo htmlspecialchars($_SESSION["First_Name"]); ?>
                </p>
                <!-- <p class="text-gray-900 dark:text-white text-lg font-medium">John Doe</p> -->
                <p class="text-gray-500 dark:text-gray-400 text-sm">Student</p>
            </div>
        </div>

        <ul class="space-y-2 font-medium">
            <!-- Other sidebar items here -->
            <li>
                <a href="dashboard.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group active-dashboard">
                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                        <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                        <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                    </svg>
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="e-books.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group active-e-books">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h6zM4 4v10h6V4H4zm7 0h6a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-6V4z" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">E-Books</span>
                </a>
            </li>
            <li>
                <a href="books.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group active-books_first">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 2a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2zm1 1v14h10V3H5zM7 4h6v2H7V4z" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Books</span>
                </a>
            </li>
            <li>
                <a href="borrow.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group active-borrow">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7 7h6v6H7V7zm2-5a1 1 0 0 0-1 1v1H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h6a1 1 0 0 0 1-1V8h1a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1h-1V1a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1z" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Borrow</span>
                </a>
            </li>

            <?php
            include '../connection.php';  // Include your database connection

            // Check if student is logged in
            if (!isset($_SESSION["Student_Id"])) {
                // Handle the case where the student is not logged in
                die('Student not logged in');
            }

            $studentId = intval($_SESSION["Student_Id"]);

            // Query to count the failed-to-claim records for the student
            $query = "SELECT COUNT(*) AS failedCount FROM borrow WHERE student_id = ?  AND status IN ('failed-to-claim', 'borrowed', 'pending', 'lost', 'ready_to_claim')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $studentId);
            $stmt->execute();
            $stmt->bind_result($failedCount);
            $stmt->fetch();
            $stmt->close();
            ?>


            <li>
                <a href="activity_log.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group active-activity-logs">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M12 2a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h8zm0 2H4v12h8V4zm4 4a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-2v2h-2v-2H8v2H6v-2H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1h2V6h2v2h2V9h2a1 1 0 0 1 1 1z" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Activity Log</span>
                    <span class="inline-flex items-center justify-center w-3 h-3 p-3 ms-3 text-sm font-medium text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">
                        <?php echo $failedCount; ?> <!-- Display the dynamic failed count here -->
                    </span>
                </a>
            </li>


        </ul>
    </div>
    <div class="px-3 py-4 border-t border-gray-200 bg-gray-50 dark:border-gray-700">
        <ul class="space-y-2 font-medium">
            <li>
                <a href="settings.php" class="flex items-center p-2 text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group active-settings">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8.5c-1.5 0-2.9.7-3.8 1.8l-1.4-1.4C6.5 8.4 7.1 7 8 6.4V5c0-1.1.9-2 2-2s2 .9 2 2v1.4c.9.6 1.5 1.7 1.2 2.7l-1.4 1.4c-.9-.8-2.3-1.3-3.2-1.3zm-1 3.8c.6-.6 1.5-.9 2.3-.6.8.2 1.4.8 1.6 1.6.2.8 0 1.7-.6 2.3-.7.6-1.6.9-2.4.6-3.4-.3-.8-1.1-1.5-2-1.5-1.1 0-2 .9-2 2s.9 2 2 2c.4 0 .8-.1 1.1-.3.5.3.9.8 1.1 1.4.1.4.2.9.1 1.4-.3.7-1.2 1.3-1.7 2.2-.3.7-.4 1.5-.3 2.2s.3 1.5.8 2.1c.7.7 1.7 1.2 2.6 1.2s2-1.1 2-2.5c0-1.4-1-2.5-2.5-2.5-.4 0-.8.1-1.2.2-.5-.4-1-1-1.3-1.7-.4-1.2.1-2.4 1.2-2.8zm-3.6 3.8c-.1.6-.4 1.1-.8 1.5s-1 .6-1.5.6c-1.1 0-2-.9-2-2s.9-2 2-2c.5 0 1.1.2 1.5.6.4.4.7.9.8 1.5zm4.6-.8c.1.3.1.6.1 1s-.1.7-.1 1.1c-1.6-.5-2.9-2.1-2.9-3.8 0-1.8 1.2-3.3 2.9-3.8-.1.4-.1.8-.1 1.1s.1.7.1 1.1c1.6-.5 2.9-2.1 2.9-3.8 0-1.8-1.2-3.3-2.9-3.8.1.3.1.7.1 1.1s-.1.7-.1 1.1c-1.6-.5-2.9-2.1-2.9-3.8s1.2-3.3 2.9-3.8c.1.3.1.7.1 1.1s-.1.7-.1 1.1c-1.6-.5-2.9-2.1-2.9-3.8 0-1.8 1.2-3.3 2.9-3.8z" />
                    </svg>
                    <span class="ms-3">Settings</span>
                </a>
            </li>
            <li>
                <a href="logout.php" class="flex items-center p-2 text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 16l-4-4 4-4v3h4v2h-4v3zm6-12h-6v2h6v-2zm2 0c1.1 0 2 .9 2 2v16c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2h12z" />
                    </svg>
                    <span class="ms-3">Log Out</span>
                </a>
            </li>

        </ul>
    </div>
</aside>