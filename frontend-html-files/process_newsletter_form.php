<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize email
    $email = filter_input(INPUT_POST, 'EMAIL', FILTER_SANITIZE_EMAIL);
    
    // Validate email
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email address is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address format";
    }
    
    // If no errors, proceed with email sending
    if (empty($errors)) {
        // Email configuration
        $to = "info@app.zestbankmy.com"; // Replace with your email address
        $subject = "Relix Bank Newsletter Subscription";
        $from = "noreply@app.zestbankmy.com"; // Replace with your domain
        
        // Email headers
        $headers = "From: $from\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Email body
        $message = "<html><body>";
        $message .= "<h2>New Newsletter Subscription</h2>";
        $message .= "<p><strong>Email Address:</strong> " . htmlspecialchars($email) . "</p>";
        $message .= "</body></html>";
        
        // Send email
        if (mail($to, $subject, $message, $headers)) {
            // Redirect to a success page or back to form with success message
            header("Location: index.html?status=success");
            exit();
        } else {
            // Redirect with error message
            header("Location: index.html?status=error");
            exit();
        }
    } else {
        // Redirect with validation errors
        $error_message = urlencode(implode(", ", $errors));
        header("Location: index.html?status=error&message=$error_message");
        exit();
    }
} else {
    // If not a POST request, redirect back to form
    header("Location: index.html");
    exit();
}
?>