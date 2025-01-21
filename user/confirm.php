<?php
// Initialize the session
include '../connection2.php'; // Include your database connection script

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: ../index.php');
  exit;
}

// Check if the session for the book bag is set
if (!isset($_SESSION['book_bag'])) {
  $_SESSION['book_bag'] = [];
}

// Redirect to borrow.php if selected_date or selected_time is empty
if (empty($_SESSION['selected_date']) || empty($_SESSION['selected_time'])) {
  header("Location: dashboard.php");
  exit(); // Ensure script stops execution after redirect
}

// Populate bookBagTitles
$bookBag = $_SESSION['book_bag'];
$bookBagTitles = array_map(function ($book) {
  return $book['id'] . ' | ' . $book['table'] . ' | ' . $book['title'] . ' | ' . $book['author'] . ' | ' . $book['publicationDate'] . ' | ' . $book['volume'] . ' | ' . $book['edition'];
}, $bookBag);

// Get image paths for each book in the book bag
$imagePaths = [];
foreach ($bookBag as $book) {
  // Sanitize table and id for safe SQL use
  $table = $book['table'];
  $id = intval($book['id']); // Ensure $id is an integer

  // Use a prepared statement to safely execute the query
  $sql = "SELECT image_path FROM `$table` WHERE id = ?";
  $stmt = $conn2->prepare($sql);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();

  // Fetch the image path if available
  if ($row = $result->fetch_assoc()) {
    $imagePaths[] = $row['image_path'];
  }

  $stmt->close();
}

// Format the selected date and time
$selectedDate = $_SESSION['selected_date'];
$selectedTime = $_SESSION['selected_time'];
$formattedDate = (new DateTime($selectedDate))->format('l, F j, Y');
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="path/to/your/styles.css">
  <script src="https://unpkg.com/lucide@latest"></script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.js"></script>
</head>

<body>
  <?php include './src/components/sidebar.php'; ?>

  <main id="content">
    <div class="p-4 sm:ml-64">
      <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
        <div class="w-full mx-auto bg-white shadow-md rounded-lg overflow-hidden">
          <div class="p-6">
            <form id="borrowForm" method="POST" action="borrow_books.php">
              <h1 class="text-2xl font-bold mb-4">Appointment Details</h1>



              <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 mb-6">
                <h2 class="text-1xl font-semibold">Step 4: Review Your Selection</h2>

                <p class="text-gray-700">Please review the details of your appointment. Keep in mind that this appointment is non-transferable.</p>
              </div>
              <h2 class="text-xl font-semibold mb-2">Appointment Details</h2>
              <div class="mb-6">
                <div class="grid grid-cols-2 gap-2 border border-gray-300 rounded-lg overflow-hidden">
                  <div class="bg-gray-100 p-2 font-semibold flex items-center border-b border-gray-300"><i data-lucide="user" class="w-4 h-4 mr-2"></i>Student Name</div>
                  <div class="p-2 border-b border-gray-300">
                    <?php echo htmlspecialchars($_SESSION["First_Name"]) . ' ' . htmlspecialchars($_SESSION["Last_Name"]); ?>
                  </div>
                  <div class="bg-gray-100 p-2 font-semibold flex items-center border-b border-gray-300"><i data-lucide="book" class="w-4 h-4 mr-2"></i>Books</div>
                  <div class="p-1 border-b border-gray-300">
                    <?php
                    if (empty($bookBagTitles)) {
                      echo 'No books added';
                    } else {
                      foreach ($bookBagTitles as $index => $bookTitle) {
                        // Split the details into separate variables
                        list($id, $table, $title, $author, $publicationDate, $volume, $edition) = explode(' | ', $bookTitle);

                        // Alternate row colors
                        $rowClass = $index % 2 === 0 ? 'bg-gray-300' : 'bg-gray-200';

                        // Display each detail in its own row with flexbox for layout
                        echo "<div class=\"{$rowClass} flex items-center justify-between p-2 m-1 rounded border border-gray-400\">";

                        // Left Section: Book Details
                        echo "<div class=\"flex-1\">";
                        if (!empty($title)) {
                          echo "<div><strong>Title:</strong> " . htmlspecialchars($title) . "</div>";
                        }
                        if (!empty($author)) {
                          echo "<div><strong>Author:</strong> " . htmlspecialchars($author) . "</div>";
                        }
                        if (!empty($publicationDate)) {
                          echo "<div><strong>Published:</strong> " . htmlspecialchars($publicationDate) . "</div>";
                        }
                        if (strtolower($volume) !== 'null') {
                          echo "<div><strong>Volume:</strong> " . htmlspecialchars($volume) . "</div>";
                        }
                        if (strtolower($edition) !== 'null') {
                          echo "<div><strong>Edition:</strong> " . htmlspecialchars($edition) . "</div>";
                        }
                        echo "</div>"; // Close left section

                        // Right Section: Book Image
                        // Get the image path for the current book
                        $imagePath = $imagePaths[$index]; // Directly fetch the image path since it's guaranteed to exist
                        echo "<div class=\"flex-shrink-0\" style=\"width: 120px; height: 160px;\">"; // Fixed width and height
                        echo "<img src=\"" . htmlspecialchars($imagePath) . "\" alt=\"Book Image\" class=\"w-full h-full object-cover rounded\">";
                        echo "</div>"; // Close right section



                        echo "</div>"; // Close the book detail container
                      }
                    }
                    ?>
                  </div>




                  <div class="bg-gray-100 p-2 font-semibold flex items-center border-b border-gray-300"><i data-lucide="calendar" class="w-4 h-4 mr-2"></i>Date To Claim</div>
                  <div class="p-2 border-b border-gray-300"><?php echo htmlspecialchars($selectedDate); ?></div>
                  <div class="bg-gray-100 p-2 font-semibold flex items-center border-b border-gray-300"><i data-lucide="clock" class="w-4 h-4 mr-2"></i>Schedule</div>
                  <div class="p-2 border-b border-gray-300"><?php echo htmlspecialchars($selectedTime); ?></div>
                </div>
              </div>

              <h2 class="text-xl font-semibold mb-2">Contact Information</h2>
              <div class="mb-6">
                <div class="grid grid-cols-2 gap-2 border border-gray-300 rounded-lg overflow-hidden">
                  <div class="bg-gray-100 p-2 font-semibold flex items-center border-b border-gray-300"><i data-lucide="mail" class="w-4 h-4 mr-2"></i>Email</div>
                  <div class="p-2 border-b border-gray-300"> <?php echo htmlspecialchars($_SESSION["email"]); ?></div>
                  <div class="bg-gray-100 p-2 font-semibold flex items-center border-b border-gray-300"><i data-lucide="phone" class="w-4 h-4 mr-2"></i>Mobile Number</div>
                  <div class="p-2 border-b border-gray-300"> <?php echo htmlspecialchars($_SESSION["phoneNo."]); ?></div>
                </div>
              </div>

              <div class="mb-6 mt-8 p-4 bg-gray-100 border-t-2 border-gray-300 rounded-lg">
                <h2 class="text-xl font-semibold mb-4">Important Instructions</h2>
                <ol class="list-decimal pl-6 text-gray-700">
                  <li>Ensure all details are correct before confirming the appointment.</li>
                  <li>The appointment is non-transferable, so please make sure to attend at the scheduled date and time.</li>
                  <li>The admin will prepare the book. Always check the dashboard to see if the book is ready to claim.</li>
                  <li>Keep in mind that any failure to claim a book will be recorded and may result in your account being banned.</li>
                </ol>
              </div>



              <div class="flex justify-between">
                <a href="schedule.php" class="bg-blue-700 text-white px-6 py-2 rounded-md hover:bg-blue-800 transition duration-300 flex items-center border border-blue-600">
                  <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back
                </a>
                <button type="button" onclick="borrowBooks()" class="bg-blue-700 text-white px-6 py-2 rounded-md hover:bg-green-800 transition duration-300 flex items-center border border-green-600">
                  <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>Confirm
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>



    <!-- Confirmation Modal -->
    <div id="confirmationModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-md" onclick="closeOnOutsideClick(event)">
      <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 md:mx-0 animate-fadeIn" onclick="event.stopPropagation()">
        <!-- Header Section -->
        <div class="p-6 border-b border-gray-200">
          <div class="flex items-center">
            <svg class="w-6 h-6 text-red-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 8v.01M12 20.8a8.8 8.8 0 110-17.6 8.8 8.8 0 010 17.6z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-red-600">Warning!</h3>
          </div>
          <p class="mt-4 text-gray-700">
            Please confirm your request to borrow the selected books. Once confirmed, this action cannot be canceled or reversed. Ensure that you have reviewed your selections carefully before proceeding.
          </p>
        </div>
        <!-- Footer Section -->
        <div class="flex justify-end p-4 rounded-b-lg bg-blue-100">
          <button type="button" class="bg-gray-600 text-white font-medium px-4 py-2 rounded-md hover:bg-gray-500 transition-colors duration-200" onclick="closeModal()">Close</button>
          <button type="button" id="confirmButton" class="bg-blue-700 text-white font-medium px-6 py-2 rounded-md hover:bg-blue-800 transition-colors duration-200 ml-2">Confirm</button>
        </div>
      </div>
    </div>
    <style>
      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: scale(0.95);
        }

        to {
          opacity: 1;
          transform: scale(1);
        }
      }

      .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
      }
    </style>



  </main>
  <script>
    function checkBookAvailability(bookId, tableName, callback) {
      fetch('check_book_availability.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            book_id: bookId,
            table: tableName
          })
        })
        .then(response => response.json())
        .then(result => {
          console.log("Availability check result for book ID " + bookId + ":", result); // Log result for each book
          if (result.success) {
            callback(true); // Book is available
          } else {
            alert(result.message || 'Book is unavailable.'); // Display error if book is unavailable

            // After the alert, unset session variables and redirect
            fetch('unset_session_and_redirect.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              }
            }).then(() => {
              console.log("Redirecting to borrow.php after unsetting session."); // Log before redirect
              window.location.href = 'borrow.php'; // Redirect to borrow.php
            });

            callback(false); // Book is not available
          }
        })
        .catch(error => {
          console.error("Error checking availability for book ID " + bookId + ":", error); // Log fetch error
          alert('An error occurred while checking book availability.');

          // After the error, unset session variables and redirect
          fetch('unset_session_and_redirect.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            }
          }).then(() => {
            console.log("Redirecting to borrow.php after error in fetch."); // Log before redirect
            window.location.href = 'borrow.php'; // Redirect to borrow.php
          });

          callback(false); // Treat as unavailable in case of error
        });
    }

    // Function to open the confirmation modal
    function openModal() {
      document.getElementById('confirmationModal').classList.remove('hidden');
    }

    // Function to close the confirmation modal
    function closeModal() {
      document.getElementById('confirmationModal').classList.add('hidden');
    }

    // Function to close the modal when clicking outside of it
    function closeOnOutsideClick(event) {
      if (event.target === document.getElementById('confirmationModal')) {
        closeModal();
      }
    }

    // Function to handle the borrow process when the user clicks 'Confirm' in the modal
    function borrowBooks() {
      const bookBag = <?php echo json_encode($bookBag); ?>;
      console.log("Book bag contents:", bookBag); // Debugging statement

      if (!bookBag.length) {
        console.error("Book bag is empty. No books to check."); // Error message if empty
        alert("No books in your bag to borrow."); // Optional alert for the user
        return;
      }

      // Show the confirmation modal
      openModal();

      // When the user clicks 'Confirm' on the modal
      document.getElementById('confirmButton').onclick = function() {
        // Hide the modal
        closeModal();

        let allAvailable = true;
        // Check each book for availability
        bookBag.forEach((book, index) => {
          console.log("Checking availability for book ID:", book.id); // Log each book check
          checkBookAvailability(book.id, book.table, function(isAvailable) {
            if (!isAvailable) {
              allAvailable = false;
            }
            // Once all books have been checked, submit the form if available
            if (index === bookBag.length - 1) {
              console.log("All books checked. Availability:", allAvailable); // Log final availability result
              if (allAvailable) {
                console.log("All books available. Submitting form."); // Log form submission
                document.getElementById('borrowForm').submit();
              }
            }
          });
        });
      };
    }
  </script>
  <script>
    lucide.createIcons();
  </script>
</body>

</html>