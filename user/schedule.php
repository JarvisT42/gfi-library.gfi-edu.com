<?php
# Initialize the session
session_start();
include("../connection.php");

$sql = "SELECT calendar FROM calendar_appointment";
$result = $conn->query($sql);

$calendar_dates = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $calendar_dates[] = $row['calendar'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="borrow.css">
  <link rel="stylesheet" href="gg.css">

  <!-- Include Tailwind CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    .hidden {
      display: none;
    }
  </style>


</head>

<body>
  <?php include './src/components/sidebar.php'; ?>

  <main id="content" class="">
    <div class="p-4 sm:ml-64">
      <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
        <div class="container">
          <div class="calendar">
            <div class="header flex flex-col sm:flex-row justify-between items-center mb-5 pb-5 border-b-2 border-gray-300">
              <div class="month text-lg font-semibold text-primary-color">July 2021</div>
              <div class="btns flex space-x-2 mt-2 sm:mt-0">
                <div class="btn today p-2 bg-primary-color text-white rounded hover:bg-red-600 transition">
                  <i class="fas fa-calendar-day"></i>
                </div>
                <div class="btn prev p-2 bg-primary-color text-white rounded hover:bg-red-600 transition">
                  <i class="fas fa-chevron-left"></i>
                </div>
                <div class="btn next p-2 bg-primary-color text-white rounded hover:bg-red-600 transition">
                  <i class="fas fa-chevron-right"></i>
                </div>
              </div>
            </div>
            <div class="weekdays grid grid-cols-7 gap-2 mb-2 text-center font-semibold">
              <div class="day">Sun</div>
              <div class="day">Mon</div>
              <div class="day">Tue</div>
              <div class="day">Wed</div>
              <div class="day">Thu</div>
              <div class="day">Fri</div>
              <div class="day">Sat</div>
            </div>
            <div class="days grid grid-cols-7 gap-2">
              <!-- render days with js -->
            </div>
          </div>
          <br>


          <div class="slot_container p-6 rounded-lg shadow-md">
            <div class="border-b border-gray-300 mb-4"></div>
            <h2 class="text-2xl font-semibold text-center mb-4">Reservation Details</h2>

            <div class="overflow-x-auto">
              <table class="min-w-full bg-gray-200 border border-gray-500 overflow-hidden">
                <thead class="border border-gray-500">
                  <tr>
                    <th colspan="3" class="py-2 text-lg text-center text-gray-700">General Santos City, South Cotabato</th>
                  </tr>
                  <tr>
                    <td colspan="3" id="selected-date-container" class="text-center py-2 text-gray-600 border border-gray-500"></td>
                  </tr>
                  <tr class="bg-gray-200 border border-gray-500">
                    <th class="px-4 py-2 text-gray-600 border border-gray-500">Time</th>
                    <th class="px-4 py-2 text-gray-600 border border-gray-500">Available Slots</th>
                    <th class="px-4 py-2 text-gray-600 border border-gray-500">In Percentage</th>
                  </tr>
                </thead>
                <tbody class="schedule">
                  <tr class="morning-cell">
                    <td class="w-1/2 px-1 sm:px-6 py-3 text-center border border-gray-500">
                      <div class="flex items-center justify-center cursor-pointer px-4 py-2 rounded-lg font-bold bg-blue-500 text-white hover:bg-blue-600">
                        <svg class="lucide lucide-sun mr-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <circle cx="12" cy="12" r="5"></circle>
                          <path d="M12 1v2"></path>
                          <path d="M12 21v2"></path>
                          <path d="M4.22 4.22l1.42 1.42"></path>
                          <path d="M18.36 18.36l1.42 1.42"></path>
                          <path d="M1 12h2"></path>
                          <path d="M21 12h2"></path>
                          <path d="M4.22 19.78l1.42-1.42"></path>
                          <path d="M18.36 5.64l1.42-1.42"></path>
                        </svg>
                        Morning
                      </div>
                    </td>
                    <td class="text-center available_slots_M border border-gray-500">10</td>
                    <td class="text-center in_percentage_M border border-gray-500">50%</td>
                  </tr>
                  <tr class="afternoon-cell">
                    <td class="w-1/2 px-1 sm:px-6 py-3 text-center border border-gray-500">
                      <div class="flex items-center justify-center cursor-pointer px-4 py-2 rounded-lg font-bold bg-blue-500 text-white hover:bg-blue-600">
                        <svg class="lucide lucide-moon mr-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M12 3C7.03 3 3 7.03 3 12s4.03 9 9 9c5.03 0 9-4.03 9-9s-4.03-9-9-9zm-2 17a7 7 0 0 1 0-14 7 7 0 0 1 0 14z"></path>
                        </svg>
                        Afternoon
                      </div>
                    </td>
                    <td class="text-center available_slots_A border border-gray-500">10</td>
                    <td class="text-center in_percentage_A border border-gray-500">50%</td>
                  </tr>
                  <!-- Add more rows for other time slots -->
                </tbody>
              </table>
            </div>

            <p id="validationMessage2" class="text-red-500 mt-4 hidden">Please select a time slot.</p>

            <form action="schedule.php" method="post" class="mt-6">
              <div class="flex justify-between gap-4">
                <button type="button" class="back-button bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg">
                  Back
                </button>
                <button type="button" class="proceed-button relative ml-2 inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 whitespace-nowrap">
                  Proceed
                </button>
              </div>
            </form>
          </div>

          <script src="https://unpkg.com/lucide@latest"></script>










        </div>
      </div>
    </div>



  </main>

  <script>
    const daysContainer = document.querySelector(".days");
    const nextBtn = document.querySelector(".next");
    const prevBtn = document.querySelector(".prev");
    const todayBtn = document.querySelector(".today");
    const month = document.querySelector(".month");
    const selectedDateContainer = document.getElementById('selected-date-container');

    const morningRows = document.querySelectorAll('.morning-cell');
    const afternoonRows = document.querySelectorAll('.afternoon-cell');
    const proceedButton = document.querySelector('.proceed-button');
    const validationMessage = document.getElementById('validationMessage2');
    const slotContainer = document.querySelector('.slot_container');

    let selectedDate = ""; // Variable to hold the selected date
    let clickedTimeSlot = ""; // Variable to track the selected time slot
    let clickedDate = null; // Variable to track the clicked date

    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    let date = new Date();
    let currentMonth = date.getMonth();
    let currentYear = date.getFullYear();

    const calendarDates = <?php echo json_encode($calendar_dates); ?>;
    const sessionData = <?php echo json_encode($_SESSION); ?>; // Get session data





    if (sessionData.selected_date) {
      const selectedDate = new Date(sessionData.selected_date);
      currentMonth = selectedDate.getMonth(); // Set to the month of selected_date
      currentYear = selectedDate.getFullYear(); // Set to the year of selected_date
    }



    const renderCalendar = () => {
      date.setDate(1);
      const firstDayIndex = new Date(currentYear, currentMonth, 1).getDay();
      const lastDay = new Date(currentYear, currentMonth + 1, 0).getDate();
      const prevLastDay = new Date(currentYear, currentMonth, 0).getDate();
      const nextDays = 7 - new Date(currentYear, currentMonth + 1, 0).getDay() - 1;

      month.innerHTML = `${months[currentMonth]} ${currentYear}`;
      daysContainer.innerHTML = "";

      // Append previous month's days
      daysContainer.innerHTML += Array.from({
          length: firstDayIndex
        }, (_, x) =>
        `<div class="day prev">${prevLastDay - firstDayIndex + x + 1}</div>`).join("");








      // Append current month's days
      daysContainer.innerHTML += Array.from({
        length: lastDay
      }, (_, i) => {
        const day = i + 1;
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const today = new Date();
        const currentDate = new Date(currentYear, currentMonth, day);

        const isToday = day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear();
        const isFutureDate = currentDate > today; // Check if the current date is in the future
        const isHighlighted = isFutureDate && calendarDates.includes(dateStr); // Highlight only future dates

        // Check if the day is the clicked date to apply pink color
        const isClickedDate = clickedDate === dateStr;

        return `<div class="day ${isToday ? 'today' : ''} ${isHighlighted ? 'highlighted' : ''}" data-date="${dateStr}" style="${isHighlighted ? (isClickedDate ? 'background-color: #ec4899; color: #fff;' : 'background-color: #8aafff; color: #fff;') : ''}">${day}</div>`;
      }).join("");

      // Append next month's days
      daysContainer.innerHTML += Array.from({
          length: nextDays
        }, (_, j) =>
        `<div class="day next">${j + 1}</div>`).join("");

      hideTodayBtn();

      // Add click event listener to highlighted days
      document.querySelectorAll('.highlighted').forEach(dayElement => {
        dayElement.addEventListener('click', () => {
          // Reset previously clicked date back to blue
          if (clickedDate) {
            const previousDay = document.querySelector(`[data-date="${clickedDate}"]`);
            if (previousDay) {
              previousDay.style.backgroundColor = '#8aafff'; // Change back to blue
              previousDay.style.color = '#fff'; // Reset text color
            }
          }

          // Change the clicked date to pink
          dayElement.style.backgroundColor = '#0a56f9'; // Change to coloy date background
          dayElement.style.color = '#fff'; // Change text color to white
          clickedDate = dayElement.getAttribute('data-date'); // Store the currently clicked date

          // Show the slot container
          slotContainer.classList.remove('hidden');

          // Get the selected date's full name
          const selectedDate = new Date(clickedDate);
          const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          };
          const formattedDate = selectedDate.toLocaleDateString(undefined, options);

          // Display the formatted date in the selected date container
          selectedDateContainer.textContent = formattedDate;
        });
      });



      if (sessionData.selected_date) {
        const dateToHighlight = new Date(sessionData.selected_date);
        const formattedDate = `${dateToHighlight.getFullYear()}-${String(dateToHighlight.getMonth() + 1).padStart(2, '0')}-${String(dateToHighlight.getDate()).padStart(2, '0')}`;
        const highlightedDay = document.querySelector(`[data-date="${formattedDate}"]`);

        if (highlightedDay) {
          highlightedDay.style.backgroundColor = '#ec4899'; // Change to pink
          highlightedDay.style.color = '#fff'; // Change text color
          clickedDate = formattedDate; // Store the highlighted date
        }
      }

      if (sessionData.selected_time) {
        const timeSlotClass = sessionData.selected_time === 'morning' ? '.morning-cell' : '.afternoon-cell';
        const selectedTimeSlot = document.querySelector(timeSlotClass);

        if (selectedTimeSlot) {
          selectedTimeSlot.style.backgroundColor = '#ec4899'; // Change to pink for the selected time slot
        }
      }


      // Check if session data is available
      if (sessionData.selected_date && sessionData.selected_time) {
        // Show the slot container
        slotContainer.classList.remove('hidden'); // Make the slot container visible

        // Highlight the date
        const dateToHighlight = new Date(sessionData.selected_date);
        const formattedDate = `${dateToHighlight.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}`;

        // Display the selected date in the container
        selectedDateContainer.textContent = formattedDate;

        const dateString = `${dateToHighlight.getFullYear()}-${String(dateToHighlight.getMonth() + 1).padStart(2, '0')}-${String(dateToHighlight.getDate()).padStart(2, '0')}`;
        const highlightedDay = document.querySelector(`[data-date="${dateString}"]`);

        if (highlightedDay) {
          highlightedDay.style.backgroundColor = '#0a56f9'; // Change blue
          highlightedDay.style.color = '#fff'; // Change text color
          clickedDate = dateString; // Store the highlighted date
        }

        // Highlight the corresponding time slot
        const timeSlotClass = sessionData.selected_time === 'morning' ? '.morning-cell' : '.afternoon-cell';
        const selectedTimeSlot = document.querySelector(timeSlotClass);

        if (selectedTimeSlot) {
          selectedTimeSlot.style.backgroundColor = '#0a56f9'; // Change to blue for the selected time slot
        }

      }




    };

    document.querySelectorAll('.morning-cell, .afternoon-cell').forEach(cell => {
      cell.addEventListener('click', () => {
        // Reset previously clicked time slot back to original color
        document.querySelectorAll('.morning-cell, .afternoon-cell').forEach(c => {
          c.style.backgroundColor = ''; // Reset background
        });
        cell.style.backgroundColor = '#0a56f9'; // Highlight the clicked cell
        clickedTimeSlot = cell.classList.contains('morning-cell') ? 'morning' : 'afternoon'; // Set time slot
      });
    });

    proceedButton.addEventListener('click', () => {
      // Check if a time slot has not been clicked and there's no session data for selected time
      if (!clickedTimeSlot && !sessionData.selected_time) {
        validationMessage.classList.remove('hidden'); // Show validation message
      } else {
        validationMessage.classList.add('hidden'); // Hide validation message

        // Send selected date and time to the server
        fetch('session_handler.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              date: clickedDate || sessionData.selected_date, // Use clickedDate or session data
              time: clickedTimeSlot || sessionData.selected_time // Use clickedTimeSlot or session data
            }),
          })
          .then(response => response.json())
          .then(data => {
            console.log(data); // Handle response if needed
            // Redirect to confirm.php
            window.location.href = "confirm.php";
          })
          .catch(error => console.error('Error:', error));
      }
    });




    // Initialize by hiding the slot container
    slotContainer.classList.add('hidden');

    // Call the render function on month change
    const changeMonth = (increment) => {
      currentMonth += increment;
      if (currentMonth > 11) currentMonth = 0, currentYear++;
      if (currentMonth < 0) currentMonth = 11, currentYear--;
      renderCalendar();
    };

    const isToday = (day) => day === new Date().getDate() && currentMonth === new Date().getMonth() && currentYear === new Date().getFullYear();

    const hideTodayBtn = () => {
      todayBtn.style.display = isToday(date.getDate()) ? "none" : "flex";
    };

    nextBtn.addEventListener("click", () => changeMonth(1));
    prevBtn.addEventListener("click", () => changeMonth(-1));
    todayBtn.addEventListener("click", () => {
      currentMonth = new Date().getMonth();
      currentYear = new Date().getFullYear();
      renderCalendar();
    });

    renderCalendar();
  </script>


  <script>
    setTimeout(() => {
      fetch('clear_session.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
        })
        .then(response => response.json())
        .then(data => {
          console.log('Response from clear session:', data);
        })
        .catch(error => console.error('Error:', error));
    }, 7200000); // 5 seconds
  </script>
  <!-- }, 7200000); // 7200000 milliseconds = 2 hours -->

</body>

</html>