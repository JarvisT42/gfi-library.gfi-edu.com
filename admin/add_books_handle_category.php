<?php
session_start();
include("../connection.php");
include("../connection2.php");

header('Content-Type: application/json');

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
        $borrowable = $_POST['borrowable'] ?? '';

        // Handle new category creation
        if (!empty($add_category)) {
            $add_category_sanitized = mysqli_real_escape_string($conn2, $add_category);

            $check_table_sql = "SHOW TABLES LIKE '$add_category_sanitized'";
            $result = $conn2->query($check_table_sql);

            if ($result->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Category already exists.']);
                exit;
            } else {
                $sql = "CREATE TABLE `$add_category_sanitized` (
                    id INT(11) AUTO_INCREMENT PRIMARY KEY,
                    call_number VARCHAR(250) NOT NULL,
                    isbn VARCHAR(250),
                    department VARCHAR(250) NOT NULL,
                    title VARCHAR(250) NOT NULL,
                    author VARCHAR(250) NOT NULL,
                    volume VARCHAR(250),
                    edition VARCHAR(250),
                    publisher VARCHAR(250) NOT NULL,
                    date_of_publication_copyright VARCHAR(250) NOT NULL,
                    no_of_copies INT(11) NOT NULL,
                    date_encoded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    subjects VARCHAR(250) NULL,
                    image_name VARCHAR(250),
                    image_path VARCHAR(250),
                    price DECIMAL(10,2) NOT NULL,
                    available_to_borrow VARCHAR(250) NOT NULL,
                    archive ENUM('yes', 'no') DEFAULT 'no'
                )";


                if ($conn2->query($sql) === TRUE) {
                    $table = $add_category_sanitized;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to create category.']);
                    exit;
                }
            }
        }

        // Handle image upload
        $imageName = $_FILES['image']['name'] ?? 'default_cover.png';
        $imageTmpName = $_FILES['image']['tmp_name'] ?? null;
        $uploadDir = '../uploads/';
        $imagePath = $uploadDir . basename($imageName);

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if ($imageTmpName && mime_content_type($imageTmpName)) {
            if (!move_uploaded_file($imageTmpName, $imagePath)) {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
                exit;
            }
        } else {
            $imagePath = '../uploads/defaultCover/default_cover.png';
        }

        // Insert main book data
        if (!empty($table)) {
            $insert_sql = "INSERT INTO `$table` (Call_Number, ISBN, Department, Title, Author, Publisher, Date_Of_Publication_Copyright, No_Of_Copies, Price, Image_Name, Image_Path, Available_To_Borrow) 
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
                    $borrowable
                );

                if ($stmt->execute()) {
                    $book_id = $stmt->insert_id;
                    $stmt->close();

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
                                echo json_encode(['success' => false, 'message' => 'Duplicate accession number: ' . $accession_no]);
                                exit;
                            }

                            // Insert into accession_records
                            $accession_insert_sql = "INSERT INTO accession_records (accession_no, call_number, book_id, book_category, available) VALUES (?, ?, ?, ?, ?)";
                            $accession_stmt = $conn->prepare($accession_insert_sql);
                            if ($accession_stmt) {
                                $accession_stmt->bind_param("ssiss", $accession_no, $call_number, $book_id, $table, $borrowable);
                                $accession_stmt->execute();
                                $accession_stmt->close();
                            }
                        }
                    }

                    echo json_encode(['success' => true, 'message' => 'Book added successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add book.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Category not specified.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $conn->close();
    $conn2->close();
}
