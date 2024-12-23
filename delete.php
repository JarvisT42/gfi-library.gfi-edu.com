<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Box</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full viewport height */
            margin: 0; /* Remove default margin */
            background-color: #f4f4f4; /* Optional: Add a subtle background */
        }

        .sign-in-up {
            background: -webkit-linear-gradient(left, #d4cd00, #A41312);
            padding: 3%;
            color: white;
            font-size: 1.5rem;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            height: 150px;
            width: 300px;
            position: relative;
            overflow: hidden;

        }

        .shape {
    position: absolute;
    width: 0;
    height: 0;
    left: 10px;
    top: -30px;
    border-left: 100px solid transparent; /* Adjust the size of the left side */
    border-right: 100px solid transparent; /* Adjust the size of the right side */
    border-top: 300px solid #A41312; /* Adjust the size and color of the top side */
}

    </style>
</head>
<body>
    <div class="sign-in-up">
        <div class="shape"></div>
    </div>
</body>
</html>
