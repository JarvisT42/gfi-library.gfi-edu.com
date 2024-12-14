<?php
require '../connection2.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get the selected table from the GET request
    if (isset($_GET['table'])) {
        $selectedTable = $_GET['table'];
        
        // If "All Fields" is selected, fetch only Title and Author from all tables except "e-books"
        if ($selectedTable === 'All Fields') {
            // Query all table names in the database
            $tablesResult = mysqli_query($conn2, "SHOW TABLES");

            if (!$tablesResult) {
                die("Error retrieving table names: " . mysqli_error($conn2));
            }
            
            // Loop through all tables and query them one by one
            while ($row = mysqli_fetch_row($tablesResult)) {
                $tableName = $row[0];
                
                // Skip the "e-books" table
                if ($tableName === 'e-books') {
                    continue; // Skip this table
                }

                // Safely escape the table name to avoid SQL injection
                $tableName = $conn2->real_escape_string($tableName);
                
                // Check if the table has the required columns ("Title" and "Author")
                $columnsResult = mysqli_query($conn2, "DESCRIBE `$tableName`");

                if (!$columnsResult) {
                    echo "Error retrieving columns for table '$tableName': " . mysqli_error($conn2);
                    continue; // Skip this table if there's an error
                }

                // Check if 'Title' and 'Author' columns exist in the current table
                $hasTitleColumn = false;
                $hasAuthorColumn = false;
                while ($column = mysqli_fetch_assoc($columnsResult)) {
                    if ($column['Field'] === 'Title') {
                        $hasTitleColumn = true;
                    }
                    if ($column['Field'] === 'Author') {
                        $hasAuthorColumn = true;
                    }
                }

                // Only query the table if it has both 'Title' and 'Author' columns
                if ($hasTitleColumn && $hasAuthorColumn) {
                    // Prepare and execute the query for the table with only Title and Author columns
                    $sql = "SELECT Title, Author FROM `$tableName`";
                    $result = mysqli_query($conn2, $sql);
                    
                    // Check if the query was successful
                    if (!$result) {
                        echo "Error executing query for table '$tableName': " . mysqli_error($conn2);
                        continue; // Skip to the next table if there's an error
                    }

                    // Display data for each table (optional: customize based on your needs)
                    echo "<h3>Data from Table: $tableName</h3>";
                    echo "<table class='table table-bordered' width='100%' cellspacing='0'>";
                    echo "<thead><tr><th>No.</th><th>Title</th><th>Author</th></tr></thead><tbody>";

                    // Initialize a counter variable for the row number
                    $counter = 1;

                    // Display rows from the current table
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $counter++ . "</td>";  // Display the row number
                        echo "<td>" . htmlspecialchars($row['Title'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['Author'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody></table>";
                }
            }
        } else {
            // Query the selected table directly
            $sql = "SELECT * FROM `$selectedTable`"; // Ensure `$selectedTable` is safe to use
            $result = mysqli_query($conn2, $sql);

            // Check if query execution is successful
            if (!$result) {
                die("Error executing query for '$selectedTable': " . mysqli_error($conn2));
            }

            // Display the selected table
            echo "Selected Table: " . htmlspecialchars($selectedTable, ENT_QUOTES, 'UTF-8');
        }
    } else {
        echo "No table selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple HTML Table</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        /* Set fixed width for Title and Author columns */
        th:nth-child(2), td:nth-child(2) {
            width: 200px; /* Title column width */
        }
        th:nth-child(3), td:nth-child(3) {
            width: 200px; /* Author column width */
        }
        /* Set smaller width for 'No.' column */
        th:nth-child(1), td:nth-child(1) {
            width: 20px; /* No. column width */
        }
    </style>
</head>
<body>

<h2>Sample HTML Table</h2>

<table class="table table-bordered" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th>No.</th>
            <th>Title</th>
            <th>Author</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (isset($result) && mysqli_num_rows($result) > 0) {
            // Fetch and display the rows
            $counter = 1;  // Initialize row counter
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $counter++ . "</td>";  // Display row number
                echo "<td>" . htmlspecialchars($row['Title'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlspecialchars($row['Author'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No data found</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
