<?php
session_start();
include '../connection2.php'; // Ensure you have your database connection
include '../connection.php'; // Ensure you have your database connection

// Check if `id` and `table` (category) exist in the URL
if (isset($_GET['id']) && isset($_GET['table'])) {
    $book_id = $_GET['id'];
    $category = $_GET['table'];
} else {
    // Redirect if no ID or table is provided
    echo "<script>window.location.href='books.php';</script>";
    exit;
}




if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['update'])) {
        $call_number = htmlspecialchars($_POST['call_number']);
        $isbn = htmlspecialchars($_POST['isbn']);
        $department = htmlspecialchars($_POST['department']);
        $title = htmlspecialchars($_POST['book_title']);
        $author = htmlspecialchars($_POST['author']);
        $publisher = htmlspecialchars($_POST['publisher_name']);
        $no_of_copies = intval($_POST['book_copies']);
        $subjects = htmlspecialchars($_POST['subject']);
        $volume = htmlspecialchars($_POST['volume']); // Get the volume
        $edition = htmlspecialchars($_POST['edition']); // Get the edition

        // Prepare the SQL update query to include volume and edition
        $sql = "UPDATE `$category` SET isbn = ?, Call_Number = ?, Department = ?, Title = ?, Author = ?, Publisher = ?, No_Of_Copies = ?, Subjects = ?, Volume = ?, Edition = ? WHERE id = ?";

        $stmt = $conn2->prepare($sql);
        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("ssssssssssi", $isbn, $call_number, $department, $title, $author, $publisher, $no_of_copies, $subjects, $volume, $edition, $book_id);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect to the same page with success message
                header("Location: " . $_SERVER['PHP_SELF'] . "?id=$book_id&table=$category&update_success=1");
                exit;
            } else {
                echo "<script>alert('Error updating book details.');</script>";
            }
        }
    }
}




// Fetch the book details if the record exists
$sql = "SELECT * FROM `$category` WHERE id = ? AND archive != 'yes'";
$stmt = $conn2->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $book = $result->fetch_assoc();
    $isbn = $book['isbn'];
    $call_number = $book['Call_Number'];
    $department = $book['Department'];
    $title = $book['Title'];
    $author = $book['Author'];
    $volume = $book['volume'];

    $edition = $book['edition'];

    $publisher = $book['Publisher'];
    $no_of_copies = $book['No_Of_Copies'];
    $date_of_publication = $book['Date_Of_Publication_Copyright'];
    $date_encoded = $book['Date_Encoded'];
    $subjects = $book['Subjects'];

    $record_cover = $book['record_cover'];


    $available_to_borrow = $book['Available_To_Borrow'];
} else {
    echo "<script> window.location.href='books.php';</script>";
    exit;
}
?>




<!DOCTYPE html>
<html lang="en">
<?php include 'admin_header.php'; ?>


<body>
    <?php include './src/components/sidebar.php'; ?>

    <main id="content" class="">
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
                <!-- Title Box -->
                <?php include './src/components/books.php'; ?>

                <?php if (isset($_GET['update_success']) && $_GET['update_success'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Update successful!
                    </div>
                <?php endif; ?>


                <?php if (isset($_GET['duplicate']) && $_GET['duplicate'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        duplicate then what accession number duplicate
                    </div>
                <?php endif; ?>


                <?php if (isset($_GET['archive_success']) && $_GET['archive_success'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Book archived successfully!
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['archive_accession_success']) && $_GET['archive_accession_success'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Accession number archived successfully!
                    </div>
                <?php endif; ?>



  <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
The Books page allows administrators to efficiently manage and update book information. This page provides an intuitive interface to add and edit
details such as ISBN, Department, Subject, Volume, and Edition, ensuring accurate and organized book records for streamlined management and easy access.              
</div>

                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <ul class="flex flex-wrap gap-2 p-5 border border-dashed rounded-md w-full">

                    <li><a class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" href="maintenace_book.php">Back</a></li>


                        <li><a class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" href="">Edit Records</a></li>
                        <br>





                    </ul>
                </div>

                <!-- Main Content Box -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 ">
                    <div class="w-full max-w-2xl mx-auto border border-black  rounded-t-lg">
                        <div class="bg-red-800 text-white rounded-t-lg">
                            <h2 class="text-lg font-semibold p-4">Edit Book Details</h2>
                        </div>
                        <div class="p-6 bg-white rounded-b-lg shadow-md">
                        <form id="editBookForm" class="space-y-4" method="POST" enctype="multipart/form-data">
    <!-- Category -->
    <div class="grid grid-cols-3 items-center gap-4">
        <label for="category" class="text-left">Category</label>
        <input id="category" name="category" value="<?php echo htmlspecialchars($category); ?>" class="col-span-2 border rounded px-3 py-2" readonly />
    </div>

    <!-- Tracking ID -->
    <div class="grid grid-cols-3 items-center gap-4">
        <label for="isbn" class="text-left">ISBN</label>
        <input id="isbn" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>" class="col-span-2 border rounded px-3 py-2" />
    </div>

    <!-- Call Number -->
    <div class="grid grid-cols-3 items-center gap-4">
        <label for="call_number" class="text-left">CALL NUMBER:</label>
        <input id="call_number" name="call_number" value="<?php echo htmlspecialchars($call_number); ?>" class="col-span-2 border rounded px-3 py-2" />
    </div>

    <!-- Department -->
    <div class="grid grid-cols-3 items-center gap-4">
        <label for="department" class="text-left">DEPARTMENT:</label>
        <input id="department" name="department" value="<?php echo htmlspecialchars($department); ?>" class="col-span-2 border rounded px-3 py-2" />
    </div>

    <!-- Book Title -->
    <div class="grid grid-cols-3 items-center gap-4">
        <label for="book_title" class="text-left">BOOK TITLE:</label>
        <input id="book_title" name="book_title" value="<?php echo htmlspecialchars($title); ?>" class="col-span-2 border rounded px-3 py-2" />
    </div>

    <!-- Author -->
    <div class="grid grid-cols-3 items-center gap-4">
        <label for="author" class="text-left">AUTHOR:</label>
        <input id="author" name="author" value="<?php echo htmlspecialchars($author); ?>" class="col-span-2 border rounded px-3 py-2" />
    </div>

    <!-- Subject -->
    <div class="grid grid-cols-3 items-center gap-4">
        <label for="subject" class="text-left">SUBJECT:</label>
        <input id="subject" name="subject" value="<?php echo htmlspecialchars($subjects); ?>" class="col-span-2 border rounded px-3 py-2" />
    </div>

    <!-- Volume -->
    <div class="grid grid-cols-3 items-center gap-4">
        <label for="volume" class="text-left">VOLUME:</label>
        <input id="volume" name="volume" value="<?php echo htmlspecialchars($volume); ?>" class="col-span-2 border rounded px-3 py-2" />
    </div>

    <!-- Edition -->
    <div class="grid grid-cols-3 items-center gap-4">
        <label for="edition" class="text-left">EDITION:</label>
        <input id="edition" name="edition" value="<?php echo htmlspecialchars($edition); ?>" class="col-span-2 border rounded px-3 py-2" />
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end gap-4">
        <button type="submit" name="update" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Save Changes
        </button>
    </div>
</form>


                            <script>
                                // Function to preview the image after selection
                                function previewImage(event) {
                                    const file = event.target.files[0];
                                    const reader = new FileReader();

                                    reader.onload = function() {
                                        // Set the image preview source to the loaded file
                                        const imagePreview = document.getElementById('imagePreview');
                                        imagePreview.src = reader.result;
                                    };

                                    if (file) {
                                        reader.readAsDataURL(file);
                                    }
                                }
                            </script>


                            <script>
                                document.getElementById("book_copies").addEventListener("input", function() {
                                    const accessionContainer = document.querySelector('.col-span-2.border.rounded.bg-gray-50'); // Target the existing container
                                    const existingInputs = accessionContainer.querySelectorAll("input[name='accession_no[]']");
                                    const currentCount = existingInputs.length; // Count of existing inputs
                                    const requiredCount = parseInt(this.value, 10) || 0; // Value of Book Copies

                                    if (requiredCount > currentCount) {
                                        // Add new fields
                                        for (let i = currentCount + 1; i <= requiredCount; i++) {
                                            const accessionDiv = document.createElement("div");
                                            accessionDiv.classList.add("flex", "gap-2");

                                            const input = document.createElement("input");
                                            input.type = "text";
                                            input.name = "accession_no[]";
                                            input.placeholder = `Accession Number ${i}`;
                                            input.classList.add("w-full", "border", "rounded", "px-2", "py-1");

                                            accessionDiv.appendChild(input);
                                            accessionContainer.appendChild(accessionDiv);
                                        }
                                    } else if (requiredCount < currentCount) {
                                        // Remove excess fields
                                        for (let i = currentCount; i > requiredCount; i--) {
                                            accessionContainer.removeChild(accessionContainer.lastChild);
                                        }
                                    }
                                });
                            </script>
                            <?php
                            // PHP code to handle the form submission

                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="./src/components/header.js"></script>

    <script>
        // Set a timeout to hide the alert after 3 seconds (3000 ms)s
        setTimeout(function() {
            var alertElement = document.getElementById('alert');
            if (alertElement) {
                alertElement.style.display = 'none';
            }
        }, 4000);
    </script>

</body>

</html>