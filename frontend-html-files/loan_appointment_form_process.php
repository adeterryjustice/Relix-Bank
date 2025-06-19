<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust path based on your PHPMailer installation

// Configuration
$admin_email = 'info@app.zestbankmy.com'; // Replace with your email
$smtp_host = 'smtp.app.zestbankmy.com'; // Replace with your SMTP host (e.g., smtp.gmail.com)
$smtp_username = 'info@app.zestbankmy.com'; // Replace with your SMTP username
$smtp_password = 'Nothingspoil@2024'; // Replace with your SMTP password
$smtp_port = 587; // Common ports: 587 (TLS), 465 (SSL)
$site_name = 'Relix Bank';

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Function to validate email
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to check for injection attempts
function is_injected($str) {
    $injections = array('(\n+)', '(\r+)', '(\t+)', '( +)', '( +)', '(+)', '( +)');
    $inject = join('|', $injections);
    $inject = "/$inject/i";
    return preg_match($inject, $str);
}

// Initialize response message
$response = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $full_name = sanitize_input($_POST['text-553'] ?? '');
    $email = sanitize_input($_POST['email-590'] ?? '');
    $phone = sanitize_input($_POST['text-755'] ?? '');
    $preferred_date = sanitize_input($_POST['text-710'] ?? '');
    $preferred_time = sanitize_input($_POST['text-115'] ?? '');
    $address = sanitize_input($_POST['text-449'] ?? '');

    // Validate inputs
    if (empty($full_name)) {
        $errors[] = 'Full name is required.';
    }
    if (empty($email) || !is_valid_email($email) || is_injected($email)) {
        $errors[] = 'A valid email address is required.';
    }
    if (empty($phone)) {
        $errors[] = 'Phone number is required.';
    }
    if (empty($preferred_date)) {
        $errors[] = 'Preferred date is required.';
    }
    if (empty($preferred_time)) {
        $errors[] = 'Preferred time is required.';
    }
    if (empty($address)) {
        $errors[] = 'Address is required.';
    }

    // Optional: Google reCAPTCHA validation
    // $recaptcha_secret = 'your_recaptcha_secret_key';
    // $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    // $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
    // $recaptcha_data = [
    //     'secret' => $recaptcha_secret,
    //     'response' => $recaptcha_response
    // ];
    // $recaptcha_options = [
    //     'http' => [
    //         'method' => 'POST',
    //         'content' => http_build_query($recaptcha_data)
    //     ]
    // ];
    // $recaptcha_context = stream_context_create($recaptcha_options);
    // $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
    // $recaptcha_json = json_decode($recaptcha_result);
    // if (!$recaptcha_json->success) {
    //     $errors[] = 'reCAPTCHA verification failed.';
    // }

    if (empty($errors)) {
        $mail = new PHPMailer(true);

        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host = $smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_username;
            $mail->Password = $smtp_password;
            $mail->SMTPSecure = 'tls';
            $mail->Port = $smtp_port;

            // Admin email
            $mail->setFrom($admin_email, $site_name);
            $mail->addAddress($admin_email);
            $mail->Subject = 'New Loan Appointment Form Submission';
            $mail->isHTML(true);

            $admin_body = "
                <html>
                <body style='font-family: Arial, sans-serif; color: #333;'>
                    <h2>New Loan Appointment Request</h2>
                    <p>A new appointment request has been submitted through the Relix Bank website.</p>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr style='background: #f8f8f8;'>
                            <td style='padding: 10px; border: 1px solid #ddd;'><strong>Full Name</strong></td>
                            <td style='padding: 10px; border: 1px solid #ddd;'>$full_name</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #ddd;'><strong>Email</strong></td>
                            <td style='padding: 10px; border: 1px solid #ddd;'>$email</td>
                        </tr>
                        <tr style='background: #f8f8f8;'>
                            <td style='padding: 10px; border: 1px solid #ddd;'><strong>Phone</strong></td>
                            <td style='padding: 10px; border: 1px solid #ddd;'>$phone</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #ddd;'><strong>Preferred Date</strong></td>
                            <td style='padding: 10px; border: 1px solid #ddd;'>$preferred_date</td>
                        </tr>
                        <tr style='background: #f8f8f8;'>
                            <td style='padding: 10px; border: 1px solid #ddd;'><strong>Preferred Time</strong></td>
                            <td style='padding: 10px; border: 1px solid #ddd;'>$preferred_time</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #ddd;'><strong>Address</strong></td>
                            <td style='padding: 10px; border: 1px solid #ddd;'>$address</td>
                        </tr>
                    </table>
                    <p style='margin-top: 20px;'>Please review and follow up with the customer.</p>
                </body>
                </html>
            ";
            $mail->Body = $admin_body;

            // Send email to admin
            $mail->send();

            // Customer confirmation email
            $mail->clearAddresses();
            $mail->addAddress($email, $full_name);
            $mail->Subject = 'Your Loan Appointment Request - Relix Bank';
            $mail->Body = "
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <style>
                        body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; color: #333; }
                        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(243, 228, 228, 0.1); }
                        .header { background:rgb(29, 122, 243); padding: 20px; text-align: center; color: #fff; }
                        .header img { max-width: 150px; }
                        .content { padding: 30px; }
                        h1 { font-size: 24px; color:rgb(250, 245, 245); margin: 0 0 20px; }
                        p { font-size: 16px; line-height: 1.6; margin: 0 0 15px; }
                        .details { background: #f9f9f9; padding: 20px; border-radius: 5px; }
                        .details p { margin: 10px 0; font-size: 15px; }
                        .details strong { color: #004aad; }
                        .button { display: inline-block; padding: 12px 25px; background: #004aad; color: #fff; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                        .footer { background: #f4f4f4; padding: 20px; text-align: center; font-size: 14px; color: #777; }
                        @media screen and (max-width: 600px) {
                            .container { margin: 10px; }
                            .content { padding: 20px; }
                            h1 { font-size: 20px; }
                            p { font-size: 14px; }
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <img src='wp-content/uploads/2024/11/logo.png' alt='Relix Bank Logo'>
                            <h1>Thank You for Your Appointment Request</h1>
                        </div>
                        <div class='content'>
                            <p>Dear $full_name,</p>
                            <p>Thank you for scheduling a loan appointment with Relix Bank. We have received your request and will contact you soon to confirm your appointment details.</p>
                            <div class='details'>
                                <p><strong>Full Name:</strong> $full_name</p>
                                <p><strong>Email:</strong> $email</p>
                                <p><strong>Phone:</strong> $phone</p>
                                <p><strong>Preferred Date:</strong> $preferred_date</p>
                                <p><strong>Preferred Time:</strong> $preferred_time</p>
                                <p><strong>Address:</strong> $address</p>
                            </div>
                            <p>If you need to modify or cancel your appointment, please contact us at <a href='mailto:$admin_email'>$admin_email</a> or call us at +971 5869 RE-LIX.</p>
                            <a href='https://www.relixbankae.com' class='button'>Visit Our Website</a>
                        </div>
                        <div class='footer'>
                            <p>&copy; " . date('Y') . " Relix Bank. All rights reserved.<br>
                            72 Sheikh Zayed Road, Dubai, UAE. | <a href='https://www.relixbankae.com'>www.relixbankae.com</a></p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            // Send email to customer
            $mail->send();

            $response = 'Thank you! Your appointment request has been submitted successfully. You will receive a confirmation email shortly.';
        } catch (Exception $e) {
            $errors[] = "Failed to send email. Error: {$mail->ErrorInfo}";
        }
    }
}

// Display response
if (!empty($errors)) {
    $response = 'Error: ' . implode('<br>', $errors);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Submission</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; text-align: center; }
        .message { padding: 20px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="message <?php echo empty($errors) ? 'success' : 'error'; ?>">
        <h2><?php echo empty($errors) ? 'Success' : 'Error'; ?></h2>
        <p><?php echo $response; ?></p>
        <a href="index.html">Back to Form</a>
    </div>
</body>
</html>