<?php
session_start();


if (!isset($_SESSION['logged_Admin_assistant']) || $_SESSION['logged_Admin_assistant'] !== true) {
    header('Location: ../index.php');

    exit;
}


// include("../connection.php");
$con = mysqli_connect("localhost", "dnllaaww_ramoza", "Ramoza@30214087695", "dnllaaww_gfi_library");

$dateToDelete = date('Y-m-d', strtotime('-1 year'));

// SQL query to delete dates older than 1 year
$sql = "DELETE FROM calendar_appointment WHERE calendar < '$dateToDelete'";

if (mysqli_query($con, $sql)) {
} else {
    echo "Error deleting records: " . mysqli_error($con);
}

$sql = "SELECT calendar FROM calendar_appointment";

// Execute the query
$result = $con->query($sql);

if ($result && $result->num_rows >= 0) {
    $fetchedDates = array(); // Initialize the array
    while ($row = $result->fetch_assoc()) {
        $fetchedDates[] = $row['calendar'];
    }
    $fetchedDatesJson = json_encode($fetchedDates); // Convert PHP array to JSON
} else {
    echo "No dates found in the database.";
}



if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['selectedDates']) && isset($_POST['DeSelectedDates'])) {
    // Prepare statements for checking existence, insertion, and deletion
    $stmtCheck = $con->prepare("SELECT calendar, morning, afternoon FROM calendar_appointment WHERE calendar = ?");
    $stmtCheck->bind_param("s", $formattedDate);

    $stmtInsert = $con->prepare("INSERT INTO calendar_appointment (calendar, morning, afternoon ) VALUES (?,'10','10')");

    $stmtInsert->bind_param("s", $formattedDate);


    $stmtDelete = $con->prepare("DELETE FROM calendar_appointment WHERE calendar = ?");
    $stmtDelete->bind_param("s", $formattedDate);

    // Process selected dates for insertion and deselected dates for deletion
    $selectedDates = json_decode($_POST['selectedDates']);
    $DeSelectedDates = json_decode($_POST['DeSelectedDates']);

    foreach ($selectedDates as $isoDate) {
        // Convert ISO date to a different format (e.g., 'Y-m-d')
        $formattedDate = date("Y-m-d", strtotime($isoDate));

        // Check if the date exists in the database
        $stmtCheck->execute();
        $stmtCheck->store_result();


        if ($stmtCheck->num_rows === 0) {
            // If the date doesn't exist, insert it into the database
            $stmtInsert->execute();


            header("Location: {$_SERVER['REQUEST_URI']}");
        }
        if ($stmtCheck->num_rows > 0) {
            $stmtDelete->execute();
            header("Location: {$_SERVER['REQUEST_URI']}");
        }
    }




    // Close prepared statements
    $stmtCheck->close();
    $stmtInsert->close();
    $stmtDelete->close();

    // Close database connection
    $con->close();
}





?>
<!-- <span style="font-family: verdana, geneva, sans-serif;"> -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | By Code Info</title>
    <!-- Font Awesome Cdn Link -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic">
    <!-- Include Tailwind CSS -->
    <!-- Latest Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">

    <!-- Latest Flowbite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.css" rel="stylesheet" />

    <!-- Latest Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.js"></script>


</head>
<style>
    /* If you prefer inline styles, you can include them directly */
    .active-calendar {
        background-color: #f0f0f0;
        /* Example for light mode */
        color: #000;
        /* Example for light mode */
    }
</style>
<style>
    nav #manage-content a[href="appointment_date.php"] {
        color: blue;
    }


    @import url(https://fonts.googleapis.com/css?family=Poppins:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic);



    .containerCalendar {


        /* Set a fixed height as per your requirement */
        margin-top: 20px;
        /* Add overflow if content exceeds the fixed height */


        --primary-color: #f90a39;
        --text-color: #1d1d1d;
        --bg-color: #f1f1fb;





    }

    .calendar {

        /* Adjust the max-width as needed */
        padding: 30px 20px;
        border-radius: 10px;
        background-color: var(--bg-color);
        height: 580px;
        position: relative;
    }

    .calendar .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid #ccc;
    }

    .calendar .footer {
        display: flex;
        justify-content: flex-end;
        /* Aligns content to the right */
        align-items: center;
        margin-top: 20px;
        border-top: 2px solid #ccc;
    }

    .disabled {
        pointer-events: none;
        opacity: 0.5;
    }

    .cool-button {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        text-align: center;
        text-decoration: none;
        outline: none;
        border: none;
        border-radius: 5px;
        color: #fff;
        background-color: #3498db;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease;
        cursor: pointer;
        margin-top: 20px;
    }

    /* Hover effect */
    .cool-button:hover {
        background-color: #21f367;
    }

    /* Click effect */
    .cool-button:active {
        background-color: #2471a3;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }



    .calendar .header .month {
        display: flex;
        align-items: center;
        font-size: 25px;
        font-weight: 600;
        color: var(--text-color);
    }

    .calendar .header .btns {
        display: flex;
        gap: 10px;
    }

    .calendar .header .btns .btn {
        width: 50px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
        color: #fff;
        background-color: var(--primary-color);
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .calendar .header .btns .btn:hover {
        background-color: #db0933;
        transform: scale(1.05);
    }

    .weekdays {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }

    .weekdays .day {
        width: calc(100% / 7 - 10px);
        text-align: center;
        font-size: 16px;
        font-weight: 600;
    }

    .days {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .day.fetched {
        background-color: blue;
        /* Or any color you desire for fetched dates */
        /* Additional styles for fetched dates */
    }

    .days .day {
        width: calc(100% / 7 - 10px);
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
        font-size: 16px;
        font-weight: 400;
        color: var(--text-color);
        background-color: #fcfcfc;
        transition: all 0.3s;
        border: 1px solid #000;

    }

    .days .day:not(.next):not(.prev):hover {
        color: #fff;
        background-color: var(--primary-color);
        transform: scale(1.05);
    }

    .days .day.today {
        color: #130c0c;
        background-color: var(--primary-color);
    }

    .days .day.next,
    .days .day.prev {
        color: #ccc;

    }

    .day.fetched {
        background-color: lightblue;
    }
</style>
<script>
    const fetchedDates = <?php echo $fetchedDatesJson; ?>;

    // JavaScript code using fetchedDates here...
</script>




<body>
    <?php include './src/components/sidebar.php'; ?>

    <main id="content" class="">


        <div class="p-4 sm:ml-64">

            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <h1 class="text-3xl font-semibold">Edit Calendar</h1>
                </div>

                <div class="containerCalendar">
                    <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300 mb-5">
                        The Edit Calendar page allows administrators to edit the days where the library open for borrowers to visit.
                    </div>
                    <div class=" p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                        Records older than 1 year will automatically deleted
                    </div>


                    <br>
                    <br>

                    <div class="calendar">
                        <div class="header">
                            <div class="month"></div>
                            <div class="btns">
                                <div class="btn today-btn">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="btn prev-btn">
                                    <i class="fas fa-chevron-left"></i>
                                </div>
                                <div class="btn next-btn">
                                    <i class="fas fa-chevron-right"></i>
                                </div>

                            </div>
                        </div>

                        <div class="weekdays">
                            <div class="day">Sun</div>
                            <div class="day">Mon</div>
                            <div class="day">Tue</div>
                            <div class="day">Wed</div>
                            <div class="day">Thu</div>
                            <div class="day">Fri</div>
                            <div class="day">Sat</div>
                        </div>
                        <div class="days">
                            <!-- lets add days using js -->
                        </div>
                        <div class="footer">
                            <form id="datesForm" method="POST" action="calendar.php">
                                <input type="hidden" id="selectedDatesInput" name="selectedDates">
                                <input type="hidden" id="DeSelectedDatesInput" name="DeSelectedDates">
                                <button type="button" class="cool-button" name="Save" onclick="submitDates()">Save</button>
                            </form>
                        </div>



                    </div>
                </div>














                <script>
                    // Function to toggle dropdown and set active
                    function toggleDropdown(dropdownId, iconId, linkId) {
                        var dropdownContent = document.querySelector('#' + dropdownId + '-content');
                        var dropdownIcon = document.getElementById(iconId);
                        var dropdownLink = document.getElementById(linkId);

                        // Get all dropdown contents except the one being toggled
                        var allDropdownContents = document.querySelectorAll('.dropdown-content');
                        allDropdownContents.forEach(function(content) {
                            if (content.id !== dropdownId + '-content') {
                                content.style.maxHeight = null; // Close other dropdown contents
                                // Remove active class from links
                                document.getElementById(content.id.replace('-content', 'Link')).classList.remove('active');
                                // Remove rotation class from icons
                                document.getElementById(content.id.replace('-content', 'Icon')).classList.remove('rotate');
                            }
                        });

                        if (dropdownContent.style.maxHeight) {
                            // If maxHeight is set, the dropdown is open, so we want to close it
                            dropdownContent.style.maxHeight = null;
                            dropdownLink.classList.remove('active'); // Remove active class from link
                        } else {
                            // If maxHeight is not set, the dropdown is closed, so we want to open it
                            dropdownContent.style.display = 'block';
                            dropdownContent.style.maxHeight = dropdownContent.scrollHeight + "px";
                            dropdownLink.classList.add('active'); // Add active class to link
                        }
                        // Toggle the rotation class regardless of opening or closing the dropdown
                        dropdownIcon.classList.toggle('rotate');
                    }

                    // Set the students dropdown active on page load
                    window.onload = function() {
                        var manageContent = document.getElementById('manage-content');
                        var manageLink = document.getElementById('manageLink');

                        manageContent.style.display = 'block';
                        manageContent.style.maxHeight = manageContent.scrollHeight + 'px';
                        manageLink.classList.add('active');
                        document.getElementById('manageIcon').classList.add('rotate');
                    };
                </script>
                <script>
                    // original// original// original// original// original// original// original// original// original// original// 
                    const daysContainer = document.querySelector(".days"),
                        nextBtn = document.querySelector(".next-btn"),
                        prevBtn = document.querySelector(".prev-btn"),
                        month = document.querySelector(".month"),
                        todayBtn = document.querySelector(".today-btn");

                    const months = [
                        "January",
                        "February",
                        "March",
                        "April",
                        "May",
                        "June",
                        "July",
                        "August",
                        "September",
                        "October",
                        "November",
                        "December",
                    ];

                    const days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

                    // get current date
                    const date = new Date();

                    // get current month
                    let currentMonth = date.getMonth();

                    // get current year
                    let currentYear = date.getFullYear();
                    let selectedDates = [];

                    let DeSelectedDates = [];
                    // function to render days
                    function renderCalendar() {
                        // get prev month current month and next month days
                        date.setDate(1);
                        const firstDay = new Date(currentYear, currentMonth, 1);
                        const lastDay = new Date(currentYear, currentMonth + 1, 0);
                        const lastDayIndex = lastDay.getDay();
                        const lastDayDate = lastDay.getDate();
                        const prevLastDay = new Date(currentYear, currentMonth, 0);
                        const prevLastDayDate = prevLastDay.getDate();
                        const nextDays = 7 - lastDayIndex - 1;

                        // update current year and month in header
                        month.innerHTML = `${months[currentMonth]} ${currentYear}`;

                        // update days html
                        let days = "";

                        // prev days html
                        for (let x = firstDay.getDay(); x > 0; x--) {
                            days += `<div class="day prev">${prevLastDayDate - x + 1}</div>`;

                        }

                        // current month days
                        for (let i = 1; i <= lastDayDate; i++) {
                            // check if its today then add today class
                            if (
                                i === new Date().getDate() &&
                                currentMonth === new Date().getMonth() &&
                                currentYear === new Date().getFullYear()
                            ) {
                                // if date month year matches add today

                                days += `<div class="day today">${i}</div>`;

                            } else {
                                //else dont add today
                                days += `<div class="day">${i}</div>`;
                            }
                        }

                        // next MOnth days
                        for (let j = 1; j <= nextDays; j++) {
                            days += `<div class="day next">${j}</div>`;

                        }


                        // run this function with every calendar render
                        hideTodayBtn();
                        daysContainer.innerHTML = days;

                        // Add event listeners to the newly generated days
                        const calendarDays = document.querySelectorAll('.day');
                        calendarDays.forEach((day) => {
                            const year = currentYear;
                            const month = currentMonth + 1; // Months in JavaScript are 0-indexed
                            const dayNum = parseInt(day.textContent);
                            const formattedDate = `${year}-${month.toString().padStart(2, '0')}-${dayNum.toString().padStart(2, '0')}`;

                            // Function to check if a date is in the fetchedDates array
                            function isInFetchedDates(dateString) {
                                return fetchedDates.includes(dateString);
                            }

                            // Function to toggle the background color of the day
                            function toggleBackgroundColor() {
                                if (day.style.backgroundColor === 'lightblue') {
                                    day.style.backgroundColor = '';
                                } else {
                                    day.style.backgroundColor = 'lightblue';
                                }
                            }

                            // Check if the day is in the fetchedDates array and set its initial styles
                            if (fetchedDates.includes(formattedDate)) {
                                day.classList.add('clicked');
                                if (!day.classList.contains('prev') && !day.classList.contains('next') && !day.classList.contains('today')) {
                                    day.style.backgroundColor = 'lightblue'; // Initially set to light blue for fetched dates
                                }
                            }

                            // Check if the day is in the selectedDates array and set its initial styles

                            if (selectedDates.includes(formattedDate)) {
                                day.classList.add('clicked');
                                if (!day.classList.contains('prev') && !day.classList.contains('next') && !day.classList.contains('today') && !fetchedDates.includes(formattedDate)) {
                                    day.style.backgroundColor = 'lightblue'; // thissssssssssssss to the calendar
                                } else if (!day.classList.contains('prev') && !day.classList.contains('next') && !day.classList.contains('today')) {
                                    day.style.backgroundColor = ''; // thissssssssssssss to the calendar
                                }
                            }
                            // Add click event listener
                            day.addEventListener('click', () => {
                                const clickedDate = new Date(Date.UTC(currentYear, currentMonth, parseInt(day.textContent)));

                                if (!day.classList.contains('prev') && !day.classList.contains('next') && !day.classList.contains('today') && clickedDate >= new Date()) {
                                    day.classList.toggle('clicked');
                                    toggleBackgroundColor();

                                    // Add or remove selected date from the array
                                    const isoDateString = `${clickedDate.getUTCFullYear()}-${('0' + (clickedDate.getUTCMonth() + 1)).slice(-2)}-${('0' + clickedDate.getUTCDate()).slice(-2)}`;
                                    const selectedDateIndex = selectedDates.indexOf(isoDateString);
                                    const deselectedDateIndex = DeSelectedDates.indexOf(isoDateString);

                                    if (selectedDateIndex === -1 || deselectedDateIndex === -1) {
                                        selectedDates.push(isoDateString);
                                        DeSelectedDates.push(isoDateString);
                                    } else {
                                        selectedDates.splice(selectedDateIndex, 1);
                                        DeSelectedDates.splice(deselectedDateIndex, 1);
                                    }
                                }
                            });
                        });


                    }



                    renderCalendar();

                    nextBtn.addEventListener("click", () => {
                        // increase current month by one
                        currentMonth++;
                        if (currentMonth > 11) {
                            // if month gets greater that 11 make it 0 and increase year by one
                            currentMonth = 0;
                            currentYear++;
                        }
                        // rerender calendar
                        renderCalendar();
                    });

                    // prev monyh btn
                    prevBtn.addEventListener("click", () => {
                        // increase by one
                        currentMonth--;
                        // check if let than 0 then make it 11 and deacrease year
                        if (currentMonth < 0) {
                            currentMonth = 11;
                            currentYear--;
                        }
                        renderCalendar();
                    });

                    // go to today
                    todayBtn.addEventListener("click", () => {
                        // set month and year to current
                        currentMonth = date.getMonth();
                        currentYear = date.getFullYear();
                        // rerender calendar
                        renderCalendar();
                    });
                    // lets hide today btn if its already current month and vice versa


                    function hideTodayBtn() {
                        if (
                            currentMonth === new Date().getMonth() &&
                            currentYear === new Date().getFullYear()
                        ) {
                            todayBtn.style.display = "none";
                        } else {
                            todayBtn.style.display = "flex";
                        }
                    }








                    function submitDates() {
                        const selectedDatesInput = document.getElementById('selectedDatesInput');
                        const DeSelectedDatesInput = document.getElementById('DeSelectedDatesInput');

                        selectedDatesInput.value = JSON.stringify(selectedDates);
                        DeSelectedDatesInput.value = JSON.stringify(DeSelectedDates); // Add this line

                        // Change button text to 'Saving...'
                        const button = document.querySelector('.cool-button');
                        button.textContent = 'Saving...';

                        // Disable the button to prevent multiple submissions while saving
                        button.disabled = true;

                        // Delay the form submission by 2 seconds
                        setTimeout(() => {
                            // Submit the form
                            document.getElementById('datesForm').submit();

                            // Re-enable the button after submission
                            button.disabled = false;
                        }, 2000); // 2 seconds delay

                        setTimeout(function() {
                            // Reload the webpage after 3 seconds
                            window.location.reload();
                        }, 3000);


                    }
                </script>


            </div>
        </div>

    </main>



    <script src="./src/components/header.js"></script>


</body>



<body>




</body>

</html>
</span>