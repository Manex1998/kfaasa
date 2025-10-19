<?php
session_start();

// Prevent double submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_SESSION['form_submitted'])) {
    $_SESSION['form_submitted'] = true;

    $to = "info@kfaasa.co.za";  
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone_number']);
    $message = trim($_POST['message']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p>Invalid email address.</p>";
        session_destroy(); // Allow future attempts
        exit;
    }

    // Email headers for admin
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Email to Ntoxy
    $subject = "New Message for KFAASA";
    $body = "
        <html><body>
        <h3>New Message from Contact Form</h3>
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Message:</strong><br>{$message}</p>
        </body></html>
    ";

    $sendToKFAASA = mail($to, $subject, $body, $headers);

    // Confirmation email to user
    $confirmSubject = "Thank you for contacting KFAASA";
    $confirmBody = "
        <html><body>
        <p>Dear {$name},</p>
        <p>Thank you for reaching out to Kidney Failure Awareness SA. We've received your message:</p>
        <blockquote>{$message}</blockquote>
        <p>We'll respond shortly.</p>
        <p><strong>- The KFAASA Team</strong></p>
        </body></html>
    ";
    $confirmHeaders = "From: KFAASA <{$to}>\r\n";
    $confirmHeaders .= "MIME-Version: 1.0\r\n";
    $confirmHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";

    $sendToClient = mail($email, $confirmSubject, $confirmBody, $confirmHeaders);

    // Handle result
    if ($sendToKFAASA && $sendToClient) {
        session_destroy(); // Allow future submissions
        header("Location: index.html"); // Redirect to thank-you page
        exit;
    } else {
        echo "<p>Message could not be sent. Please try again later.</p>";
        session_destroy();
        exit;
    }

} else {
    // Block direct GET access or double form submission
    header("Location: index.html");
    exit;
}
?>
