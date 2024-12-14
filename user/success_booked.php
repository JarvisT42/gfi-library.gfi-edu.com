<?php
# Initialize the session (if needed)
session_start();
// Retrieve the data from URL parameters
$firstName = htmlspecialchars($_GET['firstName']);
$lastName = htmlspecialchars($_GET['lastName']);
$email = htmlspecialchars($_GET['email']);
$phoneNo = htmlspecialchars($_GET['phoneNo']);
$selectedDate = htmlspecialchars($_GET['date']);
$selectedTime = htmlspecialchars($_GET['time']);
$bookBagTitlesStr = urldecode($_GET['books']);
$bookBagTitles = unserialize($bookBagTitlesStr);
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
  <title>Book Bag</title>
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
            <h1 class="text-2xl font-bold mb-4">Appointment Details</h1>
            <div class="bg-green-100 border border-green-800 rounded-lg p-4 mb-6">
              <p class="text-gray-700">Successfully Booked</p>
            </div>
            <h2 class="text-xl font-semibold mb-2">Appointment Details</h2>
            <div class="mb-6">
              <div class="grid grid-cols-2 gap-2 border border-gray-300 rounded-lg overflow-hidden">
                <div class="bg-gray-100 p-2 font-semibold flex items-center border-b border-gray-300"><i data-lucide="user" class="w-4 h-4 mr-2"></i>Student Name</div>
                <div class="p-2 border-b border-gray-300"><?php echo $firstName . ' ' . $lastName; ?></div>
                <div class="bg-gray-100 p-2 font-semibold flex items-center border-b border-gray-300"><i data-lucide="book" class="w-4 h-4 mr-2"></i>Books</div>
                <div class="p-1 border-b border-gray-300">
                  <?php
                  if (empty($bookBagTitles)) {
                    echo 'No books added';
                  } else {
                    foreach ($bookBagTitles as $index => $bookTitle) {
                      list($title, $author) = explode('|', $bookTitle);
                      $rowClass = $index % 2 === 0 ? 'bg-gray-300' : 'bg-gray-200';
                      echo "<div class=\"{$rowClass} p-2 m-1 rounded border border-gray-400\">" .
                        "Title: " . htmlspecialchars($title) . " | Author: " . htmlspecialchars($author) .
                        "</div>";
                    }
                  }
                  ?>
                </div>
                <div class="bg-gray-100 p-2 font-semibold flex items-center border-b border-gray-300"><i data-lucide="calendar" class="w-4 h-4 mr-2"></i>Date To Claim</div>
                <div class="p-2 border-b border-gray-300"><?php echo $selectedDate; ?></div>
                <div class="bg-gray-100 p-2 font-semibold flex items-center border-b border-gray-300"><i data-lucide="clock" class="w-4 h-4 mr-2"></i>Schedule</div>
                <div class="p-2 border-b border-gray-300"><?php echo $selectedTime; ?></div>
              </div>
            </div>
            <h2 class="text-xl font-semibold mb-2">Contact Information</h2>
            <div class="mb-6">
              <div class="grid grid-cols-2 gap-2 border border-gray-300 rounded-lg overflow-hidden">
                <div class="bg-gray-100 p-2 font-semibold flex items-center border-b border-gray-300"><i data-lucide="mail" class="w-4 h-4 mr-2"></i>Email</div>
                <div class="p-2 border-b border-gray-300"><?php echo $email; ?></div>
                <div class="bg-gray-100 p-2 font-semibold flex items-center border-b border-gray-300"><i data-lucide="phone" class="w-4 h-4 mr-2"></i>Mobile Number</div>
                <div class="p-2 border-b border-gray-300"><?php echo $phoneNo; ?></div>
              </div>
            </div>
            <div class="flex justify-between">
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    lucide.createIcons();
  </script>
</body>

</html>