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
                    <?php echo htmlspecialchars($_SESSION["Full_Name"]); ?>
                </p>
                <!-- <p class="text-gray-900 dark:text-white text-lg font-medium">John Doe</p> -->
                <p class="text-gray-500 dark:text-gray-400 text-sm">Admin</p>
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
                <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 active-request" aria-controls="dropdown-request" data-collapse-toggle="dropdown-request">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 21">
                        <path d="M15 12a1 1 0 0 0 .962-.726l2-7A1 1 0 0 0 17 3H3.77L3.175.745A1 1 0 0 0 2.208 0H1a1 1 0 0 0 0 2h.438l.6 2.255v.019l2 7 .746 2.986A3 3 0 1 0 9 17a2.966 2.966 0 0 0-.184-1h2.368c-.118.32-.18.659-.184 1a3 3 0 1 0 3-3H6.78l-.5-2H15Z" />
                    </svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Request</span>
                    <svg class="w-3 h-3 rotate-icon transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>

                <ul id="dropdown-request" class="hidden py-2 space-y-2">
                    <li>
                        <a href="book_request.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 active-book-request">Book Request</a>
                    </li>
                    <li>
                        <a href="borrow.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 active-book-borrow">Borrow</a>
                    </li>



                    <li>
                        <a href="borrowed_books.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 active-borrowed-books">View Borrowed Books</a>
                    </li>
                    <li>
                        <a href="lost_book_replacement.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 active-replacement-books">View Lost Books Pending Replacement</a>
                    </li>


                </ul>
            </li>




            <li>
                <a href="e-books.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group active-e-books">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M16 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zM4 6h12v12H4V6zM7 8h6v6H7V8z" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">E-Books</span>
                </a>
            </li>




            <li>
                <a href="books.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group active-books">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 2a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2zM4 3v14h12V3H4z" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Books</span>
                </a>
            </li>





            <li>
                <a href="students.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group active-students">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7 7h6v6H7V7zm2-5a1 1 0 0 0-1 1v1H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h6a1 1 0 0 0 1-1V8h1a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1h-1V1a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1z" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Students</span>
                </a>
            </li>




            <li>
                <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 active-setting" aria-controls="dropdown-setting" data-collapse-toggle="dropdown-setting">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 21">
                        <path d="M15 12a1 1 0 0 0 .962-.726l2-7A1 1 0 0 0 17 3H3.77L3.175.745A1 1 0 0 0 2.208 0H1a1 1 0 0 0 0 2h.438l.6 2.255v.019l2 7 .746 2.986A3 3 0 1 0 9 17a2.966 2.966 0 0 0-.184-1h2.368c-.118.32-.18.659-.184 1a3 3 0 1 0 3-3H6.78l-.5-2H15Z" />
                    </svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Management</span>
                    <svg class="w-3 h-3 rotate-icon transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>

                <ul id="dropdown-setting" class="hidden py-2 space-y-2">
                    <li>
                        <a href="edit_fines.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 active-catalog-setting active-edit-fines">Edit Fines</a>
                    </li>
                    <li>
                        <a href="add_course.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 active-add-course">Add Course</a>
                    </li>
                    <li>
                        <a href="add_student_id.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 active-student_id">Add ID No.</a>
                    </li>

                    <!-- <li>-->
                    <!--    <a href="add_admin_account.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 active-add-account">Register</a>-->
                    <!--</li> -->

                </ul>
            </li>

            <li>
                <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 active-maintenance" aria-controls="dropdown-maintenance" data-collapse-toggle="dropdown-maintenance">
                    <!-- Wrench Icon for Maintenance -->
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.428 4.571l-3.57 3.571-4.828-4.828 3.57-3.57a2.25 2.25 0 0 1 3.181 3.181zM10.12 9.907l4.828 4.828-1.545 1.545-3.905-3.906-1.586 1.585a2.25 2.25 0 0 0 0 3.181l2.322 2.322-3.364 3.364-1.732-1.732L5.25 15.424a2.25 2.25 0 0 0-3.182-3.182L1 12.673a1.5 1.5 0 0 1 0-2.122L6.914 3.02a1.5 1.5 0 0 1 2.122 0l1.707 1.707z" />
                    </svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Maintenance</span>
                    <svg class="w-3 h-3 rotate-icon transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>

                <ul id="dropdown-maintenance" class="hidden py-2 space-y-2">
                    <li>
                        <a href="maintenace_book.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 active-catalog-setting active-maintenance_book">Add Book Data</a>
                    </li>
                </ul>
            </li>





            <li>
                <a href="calendar.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group active-calendar">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 3h14a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zm2 2v10h12V5H5zM7 7h6v6H7V7z" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Calendar</span>
                </a>
            </li>



            <li>
                <a href="report_statement.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group active-report_statement">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M15 2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zm-1 16H6V5h8v13zM8 6h4v2H8V6z" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Report Statement</span>
                </a>
            </li>
            <li>
                <a href="statement.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group active-statement">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M15 2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zm-1 16H6V5h8v13zM8 6h4v2H8V6z" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Print</span>
                </a>
            </li>

            <!--<li>-->
            <!--    <a href="report_statement.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group active-report_statement">-->
            <!--        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">-->
            <!--            <path d="M3 3h14a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zm2 2v10h12V5H5zM7 7h6v6H7V7z" />-->
            <!--        </svg>-->
            <!--        <span class="flex-1 ms-3 whitespace-nowrap">Report Statement History</span>-->
            <!--    </a>-->
            <!--</li>-->






            <!-- <li>
                <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m17.418 3.623-.018-.008a6.713 6.713 0 0 0-2.4-.569V2h1a1 1 0 1 0 0-2h-2a1 1 0 0 0-1 1v2H9.89A6.977 6.977 0 0 1 12 8v5h-2V8A5 5 0 1 0 0 8v6a1 1 0 0 0 1 1h8v4a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-4h6a1 1 0 0 0 1-1V8a5 5 0 0 0-2.582-4.377ZM6 12H4a1 1 0 0 1 0-2h2a1 1 0 0 1 0 2Z" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Inbox</span>
                    <span class="inline-flex items-center justify-center w-3 h-3 p-3 ms-3 text-sm font-medium text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">3</span>
                </a>
            </li> -->




            <!-- Add other sidebar links here -->
        </ul>
    </div>





    <div class="px-3 py-4 border-t border-gray-200 bg-gray-50 dark:border-gray-700">
        <ul class="space-y-2 font-medium">
            <!-- <li>
                <a href="settings.php" class="flex items-center p-2 text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group active-settings">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8.5c-1.5 0-2.9.7-3.8 1.8l-1.4-1.4C6.5 8.4 7.1 7 8 6.4V5c0-1.1.9-2 2-2s2 .9 2 2v1.4c.9.6 1.5 1.7 1.2 2.7l-1.4 1.4c-.9-.8-2.3-1.3-3.2-1.3zm-1 3.8c.6-.6 1.5-.9 2.3-.6.8.2 1.4.8 1.6 1.6.2.8 0 1.7-.6 2.3-.7.6-1.6.9-2.4.6-3.4-.3-.8-1.1-1.5-2-1.5-1.1 0-2 .9-2 2s.9 2 2 2c.4 0 .8-.1 1.1-.3.5.3.9.8 1.1 1.4.1.4.2.9.1 1.4-.3.7-1.2 1.3-1.7 2.2-.3.7-.4 1.5-.3 2.2s.3 1.5.8 2.1c.7.7 1.7 1.2 2.6 1.2s2-1.1 2-2.5c0-1.4-1-2.5-2.5-2.5-.4 0-.8.1-1.2.2-.5-.4-1-1-1.3-1.7-.4-1.2.1-2.4 1.2-2.8zm-3.6 3.8c-.1.6-.4 1.1-.8 1.5s-1 .6-1.5.6c-1.1 0-2-.9-2-2s.9-2 2-2c.5 0 1.1.2 1.5.6.4.4.7.9.8 1.5zm4.6-.8c.1.3.1.6.1 1s-.1.7-.1 1.1c-1.6-.5-2.9-2.1-2.9-3.8 0-1.8 1.2-3.3 2.9-3.8-.1.4-.1.8-.1 1.1s.1.7.1 1.1c1.6-.5 2.9-2.1 2.9-3.8 0-1.8-1.2-3.3-2.9-3.8.1.3.1.7.1 1.1s-.1.7-.1 1.1c-1.6-.5-2.9-2.1-2.9-3.8s1.2-3.3 2.9-3.8c.1.3.1.7.1 1.1s-.1.7-.1 1.1c-1.6-.5-2.9-2.1-2.9-3.8 0-1.8 1.2-3.3 2.9-3.8z" />
                    </svg>
                    <span class="ms-3">Settings</span>
                </a>
            </li> -->

            <li>
                <a href="archive.php" class="flex items-center p-2 text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group active-archive">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 16">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 8h11m0 0L8 4m4 4-4 4m4-11h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Archive</span>
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