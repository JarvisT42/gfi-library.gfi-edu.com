<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find My IP</title>
</head>
<body>
    <h1>Your Public IP Address is: <span id="ip"></span></h1>

    <script>
        fetch('https://api.ipify.org?format=json')
            .then(response => response.json())
            .then(data => {
                document.getElementById('ip').textContent = data.ip;
            })
            .catch(error => {
                console.error('Error fetching IP address:', error);
            });
    </script>
</body>
</html>
