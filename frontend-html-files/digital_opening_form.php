<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $full_name = filter_input(INPUT_POST, 'text-988', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email-163', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'text-989', FILTER_SANITIZE_STRING);
    
    // Validate inputs
    $errors = [];
    
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    // If no errors, proceed with email sending
    if (empty($errors)) {
        // Email configuration
        $to = "info@app.zestbankmy.com"; // Replace with your email address
        $subject = "Relix Bank Digital Opening Form Submission";
        $from = "noreply@app.zestbankmy.com"; // Replace with your domain
        
        // Email headers
        $headers = "From: $from\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Email body
        $message = "<html><body>";
        $message .= "<h2>New Digital Opening Form Submission</h2>";
        $message .= "<p><strong>Full Name:</strong> " . htmlspecialchars($full_name) . "</p>";
        $message .= "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
        $message .= "<p><strong>Phone Number:</strong> " . htmlspecialchars($phone) . "</p>";
        $message .= "</body></html>";
        
        // Send email
        if (mail($to, $subject, $message, $headers)) {
            // Redirect to a success page or back to form with success message
            header("Location: banking.html?status=success");
            exit();
        } else {
            // Redirect with error message
            header("Location: banking.html?status=error");
            exit();
        }
    } else {
        // Redirect with validation errors
        $error_message = urlencode(implode(", ", $errors));
        header("Location: banking.html?status=error&message=$error_message");
        exit();
    }
} else {
    // If not a POST request, redirect back to form
    header("Location: banking.html");
    exit();
}
?>