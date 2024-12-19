<?php
session_start();
include("../connection.php");
include("../connection2.php");
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Collect form data
        $table = $_POST['table'] ?? '';
        $add_category = $_POST['add_category'] ?? '';
        $date_of_publication_copyright = $_POST['date_of_publication_copyright'] ?? '';
        $call_number = $_POST['call_number'] ?? '';
        $isbn = $_POST['isbn'] ?? '';
        $department = $_POST['department'] ?? '';
        $book_title = $_POST['book_title'] ?? '';
        $author = $_POST['author'] ?? '';
        $book_copies = $_POST['book_copies'] ?? 1;
        $publisher_name = $_POST['publisher_name'] ?? '';
        $price = $_POST['price'] ?? '';
        $available_to_borrow = isset($_POST['available_to_borrow']) ? 'Yes' : 'No';

        // Log form data
        error_log("Form data received. Table: $table, Category to add: $add_category");

        // Handle new category creation
        if (!empty($add_category)) {
            $add_category_sanitized = mysqli_real_escape_string($conn2, $add_category);

            $check_table_sql = "SHOW TABLES LIKE '$add_category_sanitized'";
            $result = $conn2->query($check_table_sql);

            if ($result->num_rows > 0) {
                echo json_encode(['status' => 'error', 'message' => "Table \"$add_category\" already exists."]);
                exit;
            } else {


                $sql = "CREATE TABLE `$add_category_sanitized` (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    Call_Number VARCHAR(250) NOT NULL,
    isbn VARCHAR(250),
    Department VARCHAR(250) NOT NULL,
    Title VARCHAR(250) NOT NULL,
    Author VARCHAR(250) NOT NULL,
    volume VARCHAR(250), -- Add Volume column
    edition VARCHAR(250), -- Add Volume column

    Publisher VARCHAR(250) NOT NULL,
    Date_Of_Publication_Copyright VARCHAR(250) NOT NULL,
    No_Of_Copies INT(11) NOT NULL,
    Date_Encoded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Subjects VARCHAR(250) NULL,
 image_name VARCHAR(250),
                    image_path VARCHAR(250),
    price DECIMAL(10,2) NOT NULL,

    Available_To_Borrow VARCHAR(250) NOT NULL,
    archive ENUM('yes', 'no') DEFAULT 'no'
)";



                if ($conn2->query($sql) === TRUE) {
                    $table = $add_category_sanitized;
                    error_log("New category table created: $table");
                } else {
                    throw new Exception("Error creating category: " . $conn2->error);
                }
            }
        }

        // Handle image upload
        $imageName = $_FILES['image']['name'] ?? null;
        $imageTmpName = $_FILES['image']['tmp_name'] ?? null;
        $uploadDir = '../uploads/';
        $imagePath = $uploadDir . basename($imageName);

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception("Failed to create upload directory: $uploadDir");
            }
        }

        if ($imageName && $imageTmpName) {
            $fileType = mime_content_type($imageTmpName);
            if (!str_starts_with($fileType, 'image/')) {
                throw new Exception("Uploaded file is not a valid image.");
            }

            if (!move_uploaded_file($imageTmpName, $imagePath)) {
                throw new Exception("Failed to upload image: $imageName");
            }
            error_log("Image successfully uploaded: $imagePath");
        } else {
            $imageName = 'default_cover.png';
            $imagePath = '../uploads/defaultCover/default_cover.png';
            error_log("No image uploaded.");
        }

        // Insert main book data
        if (!empty($table)) {
            $insert_sql = "INSERT INTO `$table`
            (Call_Number, ISBN, Department, Title, Author, Publisher, Date_Of_Publication_Copyright, No_Of_Copies, price, image_name, image_path, Available_To_Borrow)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn2->prepare($insert_sql);
            if ($stmt) {
                $stmt->bind_param(
                    "ssssssssssss",
                    $call_number,
                    $isbn,
                    $department,
                    $book_title,
                    $author,
                    $publisher_name,
                    $date_of_publication_copyright,
                    $book_copies,
                    $price,
                    $imageName,
                    $imagePath,
                    $available_to_borrow
                );

                if ($stmt->execute()) {
                    $book_id = $stmt->insert_id;
                    $stmt->close();
                    error_log("Main book data inserted successfully. Book ID: $book_id");

                    // Insert each accession number
                    for ($i = 1; $i <= $book_copies; $i++) {
                        $accession_key = "accession_no_$i";
                        if (!empty($_POST[$accession_key])) {
                            $accession_no = htmlspecialchars($_POST[$accession_key]);

                            // Check for duplicate accession number
                            $duplicate_check_sql = "SELECT COUNT(*) FROM accession_records WHERE accession_no = ?";
                            $duplicate_check_stmt = $conn->prepare($duplicate_check_sql);
                            $duplicate_check_stmt->bind_param("s", $accession_no);
                            $duplicate_check_stmt->execute();
                            $duplicate_check_stmt->bind_result($count);
                            $duplicate_check_stmt->fetch();
                            $duplicate_check_stmt->close();

                            if ($count > 0) {
                                echo json_encode(['status' => 'error', 'message' => "Duplicate accession number found: $accession_no"]);
                                exit;
                            }

                            // Insert into accession_records
                            $accession_insert_sql = "INSERT INTO accession_records (accession_no, call_number, book_id, book_category, available) VALUES (?, ?, ?, ?, ?)";
                            $accession_stmt = $conn->prepare($accession_insert_sql);
                            if ($accession_stmt) {
                                $accession_stmt->bind_param("ssiss", $accession_no, $call_number, $book_id, $table, $available_to_borrow);
                                if (!$accession_stmt->execute()) {
                                    throw new Exception("Error inserting accession number $accession_no: " . $accession_stmt->error);
                                }
                                $accession_stmt->close();
                            } else {
                                throw new Exception("Error preparing accession insert statement: " . $conn->error);
                            }
                        }
                    }

                    echo json_encode(['status' => 'success', 'message' => "Data and accession numbers inserted successfully."]);
                } else {
                    throw new Exception("Error inserting main book data: " . $stmt->error);
                }
            } else {
                throw new Exception("Error preparing insert statement: " . $conn2->error);
            }
        } else {
            throw new Exception("No category selected or added.");
        }
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
