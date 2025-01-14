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


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Accession Form</title>
    <script>
        // The script provided above would be placed here.
    </script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 py-10 px-5">
    <div class="max-w-4xl mx-auto bg-white p-6 shadow rounded">
        <h1 class="text-2xl font-bold mb-4">Manage Book Copies</h1>

        <!-- Book Copies Controls -->
        <div class="mb-4">
            <label for="book_copies" class="block font-medium text-gray-700">Number of Book Copies:</label>
            <div class="flex items-center gap-2 mt-2">
                <button id="decrementBtn" type="button" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">-</button>
                <input id="book_copies" type="number" name="book_copies" class="w-20 border rounded px-2 py-1 text-center" value="5" readonly>
                <button id="incrementBtn" type="button" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">+</button>
            </div>
        </div>

        <!-- Dynamic Accession Number Fields -->
        <div id="accessionNumberContainer" class="space-y-4">
            <!-- Dynamically generated fields will appear here -->
        </div>

        <!-- Submit Button -->
        <!-- Submit Button -->
        <div class="mt-6">
            <button id="submitBtn" type="button" class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Submit
            </button>
        </div>

    </div>
    <?php
    $accession_sql = "SELECT available, accession_no, book_condition, editable FROM accession_records WHERE book_id = ? AND book_category = ? AND archive != 'yes'";
    $accession_stmt = $conn->prepare($accession_sql);
    $accession_stmt->bind_param("is", $book_id, $category); // Bind book_id (integer) and category (string)
    $accession_stmt->execute();
    $accession_result = $accession_stmt->get_result();

    $accession_data = [];
    $anyUnavailable = false; // Initialize the boolean to false

    if ($accession_result->num_rows > 0) {
        while ($row = $accession_result->fetch_assoc()) {
            $accession_data[] = $row; // Collect available status and accession_no

            // Check if 'available' is 'no'
            if (in_array($row['available'], ['no', 'reserved', 'borrowed'])) {
                $anyUnavailable = true;
            }
        }
    } else {
        echo "No accession records found.";
    }
    ?>
    <script>
        // Access the PHP-generated accession_data
        const accessionData = <?php echo json_encode($accession_data); ?>;

        // Initialize the initial value of book copies
        const initialCopies = parseInt(document.getElementById("book_copies").value, 10) || accessionData.length;

        // Function to update Accession Number inputs based on Book Copies value
        function updateAccessionFields(requiredCount) {
            const accessionContainer = document.getElementById("accessionNumberContainer");
            const existingInputs = accessionContainer.querySelectorAll(".accession-row");
            const currentCount = existingInputs.length;

            // Add new fields if needed
            if (requiredCount > currentCount) {
                for (let i = currentCount; i < requiredCount; i++) {
                    const accessionDiv = document.createElement("div");
                    accessionDiv.classList.add("flex", "flex-col", "gap-2", "accession-row");

                    // Accession Number Label
                    const accessionLabel = document.createElement("label");
                    accessionLabel.textContent = "Accession Number:";
                    accessionLabel.setAttribute("for", `accession_no_${i}`);
                    accessionLabel.classList.add("font-medium", "text-gray-700");

                    // Row for Accession Number, Archive Button, and Checkbox
                    const topRowDiv = document.createElement("div");
                    topRowDiv.classList.add("flex", "gap-2", "items-center");

                    // Accession Number Input
                    const input = document.createElement("input");
                    input.type = "text";
                    input.name = "accession_no[]";
                    input.id = `accession_no_${i}`;
                    input.classList.add("w-full", "border", "rounded", "px-2", "py-1");

                    // Archive Button
                    const archiveButton = document.createElement("button");
                    archiveButton.type = "button";
                    archiveButton.textContent = "Archive";
                    archiveButton.classList.add("px-4", "py-2", "bg-red-500", "text-white", "rounded", "hover:bg-red-600");
                    archiveButton.addEventListener("click", function() {
                        archiveAccession(input.value, accessionDiv);
                    });

                    // Borrowable Checkbox
                    const checkboxDiv = document.createElement("div");
                    checkboxDiv.classList.add("flex", "items-center");

                    const checkbox = document.createElement("input");
                    checkbox.type = "checkbox";
                    checkbox.name = "borrowable[]";
                    checkbox.classList.add("mr-2");

                    // Set the checkbox state based on `available`
                    if (accessionData[i]) {
                        if (accessionData[i].editable === "yes") {
                            checkbox.checked = true;
                        } else {
                            checkbox.checked = false;
                        }
                    }

                    // Checkbox event listener to send data on click
                    checkbox.addEventListener("change", function() {
                        if (input.value) {
                            const bookId = "<?php echo $_GET['id']; ?>";
                            const category = "<?php echo $_GET['table']; ?>";

                            const requestData = {
                                accession_no: input.value,
                                book_id: bookId,
                                category: category,
                                borrowable: checkbox.checked ? "yes" : "no"
                            };

                            alert(`Data to be sent: ${JSON.stringify(requestData)}`);

                            fetch("edit_books_update.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify(requestData)
                                })
                                .then((response) => response.json())
                                .then((data) => {
                                    if (data.success) {
                                        alert(`Borrowable status updated for Accession No: ${input.value}.`);
                                    } else {
                                        alert(`Failed to update Borrowable status for Accession No: ${input.value}.`);
                                    }
                                })
                                .catch((error) => {
                                    console.error("Error updating borrowable status:", error);
                                    alert("An error occurred while updating borrowable status.");
                                });
                        } else {
                            alert("Invalid Accession Number.");
                            checkbox.checked = !checkbox.checked; // Revert checkbox state if invalid
                        }
                    });

                    const checkboxLabel = document.createElement("label");
                    checkboxLabel.textContent = "Set as Borrowable";

                    checkboxDiv.appendChild(checkbox);
                    checkboxDiv.appendChild(checkboxLabel);

                    topRowDiv.appendChild(input);
                    topRowDiv.appendChild(archiveButton);
                    topRowDiv.appendChild(checkboxDiv);

                    const conditionLabel = document.createElement("label");
                    conditionLabel.textContent = "Book Condition:";
                    conditionLabel.setAttribute("for", `book_condition_${i}`);
                    conditionLabel.classList.add("font-medium", "text-gray-700");

                    const conditionInput = document.createElement("textarea");
                    conditionInput.name = "book_condition[]";
                    conditionInput.id = `book_condition_${i}`;
                    conditionInput.rows = 3;
                    conditionInput.classList.add("w-full", "border", "rounded", "px-2", "py-1");

                    if (accessionData[i]) {
                        input.value = accessionData[i].accession_no;
                        conditionInput.value = accessionData[i].book_condition;
                        if (['no', 'reserved', 'borrowed'].includes(String(accessionData[i].available))) {
                            input.classList.add("bg-gray-300", "cursor-not-allowed");
                            input.readOnly = true;
                            conditionInput.classList.add("bg-gray-300", "cursor-not-allowed");
                            conditionInput.readOnly = true;
                            archiveButton.disabled = true;
                            checkbox.disabled = true;
                        }
                    } else {
                        input.placeholder = `Accession Number ${i + 1}`;
                        conditionInput.placeholder = `Condition ${i + 1}`;
                    }

                    accessionDiv.appendChild(accessionLabel);
                    accessionDiv.appendChild(topRowDiv);
                    accessionDiv.appendChild(conditionLabel);
                    accessionDiv.appendChild(conditionInput);

                    accessionContainer.appendChild(accessionDiv);
                }
            } else if (requiredCount < currentCount) {
                for (let i = currentCount; i > requiredCount; i--) {
                    accessionContainer.removeChild(accessionContainer.lastChild);
                }
            }
        }

        function archiveAccession(accessionNo, accessionDiv) {
            if (accessionNo) {
                const bookId = "<?php echo $_GET['id']; ?>";
                const category = "<?php echo $_GET['table']; ?>";

                const requestData = {
                    accession_no: accessionNo,
                    book_id: bookId,
                    category: category,
                    archive: true
                };

                alert(`Data to be sent: ${JSON.stringify(requestData)}`);

                fetch("edit_books_update.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(requestData)
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert(`Accession No: ${accessionNo} archived successfully.`);
                            accessionDiv.remove();
                        } else {
                            alert(`Failed to archive Accession No: ${accessionNo}.`);
                        }
                    })
                    .catch((error) => {
                        console.error("Error archiving accession:", error);
                        alert("An error occurred while archiving.");
                    });
            } else {
                alert("Invalid Accession Number.");
            }
        }

        document.getElementById("incrementBtn").addEventListener("click", function() {
            const bookCopiesInput = document.getElementById("book_copies");
            let currentValue = parseInt(bookCopiesInput.value) || 0;
            currentValue += 1;
            bookCopiesInput.value = currentValue;
            updateAccessionFields(currentValue);
        });

        document.getElementById("decrementBtn").addEventListener("click", function() {
            const bookCopiesInput = document.getElementById("book_copies");
            let currentValue = parseInt(bookCopiesInput.value) || 0;
            if (currentValue > initialCopies) {
                currentValue -= 1;
                bookCopiesInput.value = currentValue;
                updateAccessionFields(currentValue);
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            updateAccessionFields(initialCopies);
        });
    </script>

<script>
  document.getElementById("submitBtn").addEventListener("click", function () {
    const accessionRows = document.querySelectorAll(".accession-row");
    const bookId = "<?php echo $_GET['id']; ?>";
    const category = "<?php echo $_GET['table']; ?>";

    const dataToSubmit = Array.from(accessionRows).map((row, index) => {
        return {
            original_accession_no: accessionData[index]?.accession_no || null, // Include the original accession number
            accession_no: row.querySelector("input[name='accession_no[]']").value,
            book_condition: row.querySelector("textarea[name='book_condition[]']").value,
            borrowable: row.querySelector("input[name='borrowable[]']").checked ? "yes" : "no",
            isNew: !accessionData[index], // Mark as new if it doesn't exist in the original data
        };
    });

    const payload = {
        book_id: bookId,
        category: category,
        accession_data: dataToSubmit,
    };

    alert("Data to be sent: " + JSON.stringify(payload, null, 2)); // Optional: Inspect the payload before submission
    console.log("Data to be sent:", payload); // Log to console for debugging

    fetch("edit_books_update2.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(payload),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert("Accession numbers updated successfully.");
                location.reload(); // Reload the page to show the updated data
            } else {
                alert("Failed to update accession numbers. Please try again.");
            }
        })
        .catch((error) => {
            console.error("Error updating accession numbers:", error);
            alert("An error occurred while updating accession numbers.");
        });
});


</script>

</body>

</html>