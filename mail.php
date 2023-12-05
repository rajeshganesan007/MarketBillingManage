<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $send_to = $_POST["send_to"];
    $recipient = $_POST["recipient"];
    $message = $_POST["message"];

    if ($send_to === "email") {
        $to = $recipient;
        $subject = "Message from Email/Mobile Sender";
        $headers = "From: your_email@example.com";
        mail($to, $subject, $message, $headers);
    } elseif ($send_to === "mobile") {
        echo "Mobile message sent: $message";
    }
}
?>
