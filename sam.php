<?php
// Custom WordPress database error page
header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 600'); // 10 minutes

// Optional: Notify the administrator via email
// mail("your-email@example.com", "Database Error", "There is a problem with the database!", "From: Db Error Watching");

?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Database Error</title>
    <style>
        /* General Page Style */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        /* Error Container */
        .error-container {
            max-width: 600px;
            padding: 30px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Header Style */
        .error-container h1 {
            font-size: 3em;
            color: #dc3545;
            margin-bottom: 20px;
        }

        /* Description Style */
        .error-container p {
            font-size: 1.2em;
            color: #6c757d;
            line-height: 1.5;
            margin-bottom: 30px;
        }

        /* Action Button */
        .error-container a {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1em;
            color: #fff;
            background: #007bff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .error-container a:hover {
            background: #0056b3;
        }

        /* Optional Logo */
        .error-container img {
            max-width: 150px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <!-- Optional Logo -->
        <img src="https://via.placeholder.com/150" alt="Logo" />

        <!-- Error Message -->
        <h1>We're Sorry!</h1>
        <p>We're experiencing technical difficulties and are working to resolve the issue. Please check back soon.</p>

        <!-- Call to Action -->
        <a href="/" role="button">Go Back to Home</a>
    </div>
</body>
</html>
