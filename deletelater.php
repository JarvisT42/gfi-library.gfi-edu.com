<?php
// Include database connection
// db.php
$host = 'localhost';
$user = 'root'; // Replace with your DB username
$password = ''; // Replace with your DB password
$database = 'image_upload';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $imageName = $_FILES['image']['name'];
    $imageTmpName = $_FILES['image']['tmp_name'];
    $uploadDir = 'uploads/';
    $imagePath = $uploadDir . basename($imageName);

    // Create uploads directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move uploaded file to the uploads directory
    if (move_uploaded_file($imageTmpName, $imagePath)) {
        // Insert file data into the database
        $stmt = $conn->prepare("INSERT INTO images (name, image_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $imageName, $imagePath);

        if ($stmt->execute()) {
            echo "Image uploaded successfully!";
        } else {
            echo "Failed to save image to database.";
        }

        $stmt->close();
    } else {
        echo "Failed to upload image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload</title>
</head>
<body>
    <h2>Upload an Image</h2>
    <form action="deletelater.php" method="POST" enctype="multipart/form-data">
        <label for="image">Select Image:</label>
        <input type="file" name="image" id="image" required>
        <button type="submit">Upload</button>
    </form>

    <h2>Uploaded Images</h2>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        <?php
        // Fetch and display images from the database
        $result = $conn->query("SELECT * FROM images ORDER BY uploaded_at DESC");

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div style="border: 1px solid #ccc; padding: 10px; text-align: center;">';
                echo ' <img src="'. htmlspecialchars($row['image_path']) . '" alt="Book Cover" class="w-28 h-40 border-2 border-gray-400 rounded-lg object-cover">';

                echo '</div>';
            }
        } else {
            echo '<p>No images uploaded yet.</p>';
        }
        ?>
    </div>
</body>
</html>
