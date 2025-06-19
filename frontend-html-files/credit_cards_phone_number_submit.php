<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize phone number
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    
    // Validate phone number
    $errors = [];
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    } elseif (!preg_match("/^[0-9+\-\(\) ]{7,20}$/", $phone)) {
        $errors[] = "Invalid phone number format";
    }
    
    // If no errors, proceed with email sending
    if (empty($errors)) {
        // Email configuration
        $to = "info@app.zestbankmy.com"; // Replace with your email address
        $subject = "Relix Bank Credit Card Form Submission";
        $from = "noreply@app.zestbankmy.com"; // Replace with your domain
        
        // Email headers
        $headers = "From: $from\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Email body
        $message = "<html><body>";
        $message .= "<h2>New Credit Card Form Submission</h2>";
        $message .= "<p><strong>Phone Number:</strong> " . htmlspecialchars($phone) . "</p>";
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