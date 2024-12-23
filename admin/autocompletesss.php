<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Title Autocomplete</title>

    <!-- Include jQuery and jQuery UI -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
    </style>
</head>
<body>

    <h1>Book Title Autocomplete</h1>
    <input type="text" id="book-title" placeholder="Type book title...">

    <script>
        $(document).ready(function() {
            // jQuery UI Autocomplete
            $("#book-title").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "autocomplete.php",  // Path to your PHP script
                        dataType: "json",
                        data: {
                            term: request.term  // Send the typed input as a parameter
                        },
                        success: function(data) {
                            response(data);  // Return the titles to the input field
                        }
                    });
                },
                minLength: 1  // Trigger autocomplete after 2 characters are typed
            });
        });
    </script>

</body>
</html>
