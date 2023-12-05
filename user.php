<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["recipient_email"]) && isset($_POST["email_message"])) {
        $recipient_email = $_POST["recipient_email"];
        $email_message = $_POST["email_message"];
        $subject = "Test Email"; // Email subject
        $sender_email = "your-email@example.com"; // Replace with your email address

        // Send the email
        if (mail($recipient_email, $subject, $email_message, "From: $sender_email")) {
            echo "Email sent successfully to $recipient_email.";
        } else {
            echo "Failed to send the email.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Send Email</title>
</head>

<body>
    <h1>Send an Email</h1>
    <form method="post">
        <label for="recipient_email">Recipient's Email:</label>
        <input type="email" name="recipient_email" required><br><br>
        <label for="email_message">Email Message:</label>
        <textarea name="email_message" rows="4" cols="50" required></textarea><br><br>
        <button type="submit">Send Email</button>
    </form>
</body>

</html>
