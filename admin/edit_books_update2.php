<?php
session_start(); // Start the session

include '../connection.php'; // Primary database connection
include '../connection2.php'; // Secondary database connection

try {
    // Debugging log for incoming POST data
    file_put_contents('debug_log.txt', "POST Data:\n" . print_r($_POST, true) . "\n", FILE_APPEND);

    if (!isset($_POST['data'])) {
        throw new Exception('Missing required data.');
    }
    $data = json_decode($_POST['data'], true);

    if (!isset($data['category'], $data['isbn'], $data['department'], $data['book_title'], $data['author'], $data['book_copies'], $data['date_of_publication_copyright'])) {
        throw new Exception('Missing required fields.');
    }

    // Sanitize inputs
    $book_id = htmlspecialchars($data['book_id'], ENT_QUOTES, 'UTF-8');
    $category = htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8');
    $isbn = htmlspecialchars($data['isbn'], ENT_QUOTES, 'UTF-8');
    $call_number = htmlspecialchars($data['call_number'], ENT_QUOTES, 'UTF-8');
    $department = htmlspecialchars($data['department'], ENT_QUOTES, 'UTF-8');
    $title = htmlspecialchars($data['book_title'], ENT_QUOTES, 'UTF-8');
    $author = htmlspecialchars($data['author'], ENT_QUOTES, 'UTF-8');
    $no_of_copies = intval($data['book_copies']);
    $date_of_publication = htmlspecialchars($data['date_of_publication_copyright'], ENT_QUOTES, 'UTF-8');
    $publisher = htmlspecialchars($data['publisher_name'], ENT_QUOTES, 'UTF-8');
    $subject = htmlspecialchars($data['subject'], ENT_QUOTES, 'UTF-8');
    $accession_data = $data['accession_data'];

    // Log the accession_data in the desired format
    file_put_contents('debug_log.txt', "Accession Data:\n" . json_encode($accession_data, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

    // Handle the image upload
    $imageName = $_FILES['image']['name'] ?? null;
    $imageTmpName = $_FILES['image']['tmp_name'] ?? null;
    $uploadDir = '../uploads/';
    $imagePath = $uploadDir . basename($imageName);

    // Create uploads directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Check if an image was uploaded
    if ($imageName && $imageTmpName) {
        // Ensure the file is an image
        $fileType = mime_content_type($imageTmpName);
        if (!str_starts_with($fileType, 'image/')) {
            throw new Exception("Uploaded file is not a valid image.");
        }

        // Move the uploaded file
        if (!move_uploaded_file($imageTmpName, $imagePath)) {
            throw new Exception("Failed to upload image: $imageName");
        }
    } else {
        // Set default values if no image was uploaded
        $imageName = 'default_cover.png';
        $imagePath = $uploadDir . 'default_cover.png';
    }

    // Debug log for image handling
    file_put_contents('debug_log.txt', "Image Handling:\nName: $imageName\nPath: $imagePath\n", FILE_APPEND);

    // Update book information
    $sql = ($imageName !== 'default_cover.png') // Check if default image is used
        ? "UPDATE `$category` SET 
            isbn = ?, 
            Call_Number = ?, 
            Department = ?, 
            Title = ?, 
            Author = ?, 
            Publisher = ?, 
            No_Of_Copies = ?, 
            Date_Of_Publication_Copyright = ?, 
            Subjects = ?, 
            image_name = ?, 
            image_path = ? 
          WHERE id = ?"
        : "UPDATE `$category` SET 
            isbn = ?, 
            Call_Number = ?, 
            Department = ?, 
            Title = ?, 
            Author = ?, 
            Publisher = ?, 
            No_Of_Copies = ?, 
            Date_Of_Publication_Copyright = ?, 
            Subjects = ? 
          WHERE id = ?";

    // Prepare statement
    $stmt = $conn2->prepare($sql);

    if ($imageName !== 'default_cover.png') { // Custom image uploaded
        $stmt->bind_param(
            'ssssssissssi',
            $isbn,
            $call_number,
            $department,
            $title,
            $author,
            $publisher,
            $no_of_copies,
            $date_of_publication,
            $subject,
            $imageName,
            $imagePath,
            $book_id
        );
    } else { // Default image used
        $stmt->bind_param(
            'ssssssisss',
            $isbn,
            $call_number,
            $department,
            $title,
            $author,
            $publisher,
            $no_of_copies,
            $date_of_publication,
            $subject,
            $book_id
        );
    }

    if (!$stmt->execute()) {
        throw new Exception("Error updating book: " . $stmt->error);
    }

    // Update or insert accession records
    if (!empty($accession_data)) {
        $update_sql = "UPDATE `accession_records` SET accession_no = ? WHERE accession_no = ?";
        $insert_sql = "INSERT INTO `accession_records` (accession_no, call_number, book_id, book_category, archive, available) VALUES (?, ?, ?, ?, 'no', ?)";

        foreach ($accession_data as $record) {
            $new_accession_no = htmlspecialchars($record['new_accession_no'], ENT_QUOTES, 'UTF-8');
            $original_accession_no = htmlspecialchars($record['original_accession_no'], ENT_QUOTES, 'UTF-8');
            $available_status = htmlspecialchars($record['borrowable'], ENT_QUOTES, 'UTF-8');

            if (!empty($original_accession_no)) {
                // Update existing record
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param('ss', $new_accession_no, $original_accession_no);

                if (!$update_stmt->execute()) {
                    throw new Exception("Error updating accession record: " . $update_stmt->error);
                }
            } else {
                // Insert new record
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param('sssss', $new_accession_no, $call_number, $book_id, $category, $available_status);

                if (!$insert_stmt->execute()) {
                    throw new Exception("Error inserting accession record: " . $insert_stmt->error);
                }
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Book information and accession records updated successfully.']);
} catch (Exception $e) {
    // Log the error for debugging
    file_put_contents('debug_log.txt', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close database connections
$conn->close();
$conn2->close();
?>
