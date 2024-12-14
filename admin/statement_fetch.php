<?php
// Database connection
require '../connection2.php';

if ($conn2->connect_error) {
    die("Connection failed: " . $conn2->connect_error);
}

// Check if 'table' is passed from AJAX
if (isset($_POST['table']) && !empty($_POST['table'])) {
    $table = $_POST['table'];

    // Exclude the "e-books" table
    if ($table === 'e-books') {
        echo json_encode([]); // Return empty array if "e-books" is selected
        exit; // Stop further execution
    }

    if ($table === 'All Fields') {
        // Fetch data from all tables, excluding "e-books"
        $sql = "SHOW TABLES FROM dnllaaww_gfi_library_books_inventory"; // Replace with your actual database name
        $result = $conn2->query($sql);

        $allData = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array()) {
                $tableName = $row[0];
                
                // Skip the "e-books" table
                if ($tableName !== 'e-books') {
                    $fetchSql = "SELECT * FROM `$tableName`";
                    $fetchResult = $conn2->query($fetchSql);

                    if ($fetchResult->num_rows > 0) {
                        while ($fetchRow = $fetchResult->fetch_assoc()) {
                            $allData[] = [
                                'call_number' => $fetchRow['Call_Number'], // Replace 'author' with the actual column name

                                'title' => $fetchRow['Title'], // Replace 'title' with the actual column name
                                'author' => $fetchRow['Author'], // Replace 'author' with the actual column name
                                'publisher' => $fetchRow['Publisher'], // Replace 'author' with the actual column name
                                'no_of_copies' => $fetchRow['No_Of_Copies'], // Replace 'author' with the actual column name

                            ];
                        }
                    }
                }
            }

            echo json_encode($allData); // Return all data as JSON
        } else {
            echo json_encode([]); // Return empty array if no tables
        }
    } else {
        // Query to fetch all data from the selected table
        $sql = "SELECT * FROM `$table`";
        $result = $conn2->query($sql);

        // Prepare data to send back as JSON
        if ($result->num_rows > 0) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                // Adjust fields depending on your table structure
                $data[] = [
                    'call_number' => $row['Call_Number'], // Replace 'author' with the actual column name

                    'title' => $row['Title'],  // Replace 'title' with the actual column name
                    'author' => $row['Author'], // Replace 'author' with the actual column name
                    'publisher' => $row['Publisher'], // Replace 'author' with the actual column name
                    'no_of_copies' => $row['No_Of_Copies'], // Replace 'author' with the actual column name


                ];
            }
            echo json_encode($data); // Return data as JSON
        } else {
            echo json_encode([]); // Return empty array if no rows
        }
    }
} else {
    echo json_encode([]); // Return empty array if no table selected
}
?>
