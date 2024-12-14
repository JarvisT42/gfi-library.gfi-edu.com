<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #ffe259, #ffa751);
            font-family: Arial, sans-serif;
        }

        .navbar-brand img {
            max-height: 50px;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: #333;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #b63a29;
        }

        .btn-login {
            background-color: #b63a29;
            color: #fff;
            border-radius: 5px;
            padding: 0.5rem 1rem;
        }

        .btn-login:hover {
            background-color: #8c2921;
        }

        .footer {
            background-color: #8c2921;
            color: #fff;
            padding: 1rem 0;
            text-align: center;
        }

        .footer .social-icons a {
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .footer .social-icons a:hover {
            color: #ffcc00;
        }

        .container-termOfService {
            display: flex;
            flex-wrap: wrap;
            margin: 20px auto;
            max-width: 1200px;
        }

        .sidebar {
            flex: 1 1 100%;
            background-color: #333;
            color: #fff;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #fff;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .sidebar ul li a:hover {
            color: #ffa751;
        }

        .content {
            flex: 1 1 100%;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .content h1 {
            font-size: 2rem;
            color: #333;
        }

        .content p {
            font-size: 1rem;
            line-height: 1.6;
            color: #666;
        }

        @media (min-width: 768px) {
            .container-termOfService {
                flex-wrap: nowrap;
            }

            .sidebar {
                flex: 0 0 250px;
                margin-bottom: 0;
            }

            .content {
                margin-left: 20px;
                flex: 1;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand img {
                max-height: 40px;
            }

            .content h1 {
                font-size: 1.5rem;
            }

            .content p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>

    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="./src/assets/images/library.png" alt="Library Logo" class="mr-2">
                    <img src="./src/assets/images/350861720_596709698913796_561423606268093637_n.png" alt="logo" />
                    <span class="font-weight-bold text-dark">&nbsp;&nbsp;&nbsp;Gensantos Foundation College, Inc. Library</span>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="btn btn-login nav-link text-white" href="index.php">HOME</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container-termOfService">
        <div class="sidebar">
            <h2>Terms of Service</h2>
            <ul>
                <li><a href="#" onclick="loadContent('User Agreement')">User Agreement</a></li>
                <li><a href="#" onclick="loadContent('Terms of Use')">Terms of Use</a></li>
                <li><a href="#" onclick="loadContent('Any Hire Terms')">Any Hire Terms</a></li>
                <li><a href="#" onclick="loadContent('Escrow Instructions')">Escrow Instructions</a></li>
                <li><a href="#" onclick="loadContent('Privacy Policy')">Privacy Policy</a></li>
            </ul>
        </div>

        <div class="content" id="content-area">
            <h1>Welcome</h1>
            <p>Select a section from the sidebar to view the details.</p>
        </div>
    </div>

    <footer class="footer">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
            <span>
                © 2024 GFI FOUNDATION COLLEGE, INC. All Rights Reserved.
            </span>
            <div class="social-icons">
                <a href="#" class="text-white mx-2">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="text-white mx-2">
                    <i class="fab fa-youtube"></i>
                </a>
            </div>
        </div>
    </footer>

    <script>
        function loadContent(section) {
            const contentArea = document.getElementById("content-area");

            const contentMap = {
                "User Agreement": `<h1>User Agreement</h1><p>Details of the User Agreement...</p>`,
                "Terms of Use": `<h1>Terms of Use</h1><p>Details of the Terms of Use...</p>`,
                "Any Hire Terms": `<h1>Any Hire Terms</h1><p>Details of the Any Hire Terms...</p>`,
                "Escrow Instructions": `<h1>Escrow Instructions</h1><p>Details of the Escrow Instructions...</p>`,
                "Privacy Policy": `<h1>Privacy Policy</h1><p>Details of the Privacy Policy...</p>`
            };

            contentArea.innerHTML = contentMap[section] || `<h1>${section}</h1><p>Details not available.</p>`;
        }
    </script>

</body>

</html>
