<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Send Email</title>
</head>
<body>
    <form action="reset_password_action.php" method="post" >
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required> <br><br>

        <label for="subject">Subject:</label>
        <input type="text" name="subject" id="subject" required> <br><br>

        <label for="message">Message:</label>
        <textarea name="message" id="message" rows="5" cols="30" required></textarea> <br><br>

        <button type="submit" name="submit">Send Email</button>
    </form>
</body>
</html>
