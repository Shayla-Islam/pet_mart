<?php
/**
 * PetMart Contact Processing System
 * Version: 2.0.1
 * Features: Prepared Statements, Anti-Spam, Security Sanitization, Error Logging
 */

session_start();

// 1. Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'petmart_db');

// 2. Establishing Secure Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for Connection Errors
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("A system error occurred. Please try again later.");
}

// 3. Process Request only if Method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 4. Collect and Sanitize Basic Input
    $name    = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $email   = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $phone   = trim(mysqli_real_escape_string($conn, $_POST['phone']));
    $subject = trim(mysqli_real_escape_string($conn, $_POST['subject']));
    $message = trim(mysqli_real_escape_string($conn, $_POST['message']));

    // 5. Advanced Validation Logic
    if (empty($name) || empty($email) || empty($message)) {
        echo "<script>alert('Error: Required fields are missing.'); window.history.back();</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Error: The email address provided is invalid.'); window.history.back();</script>";
        exit();
    }

    // 6. Anti-Spam Check (Rate Limiting)
    // Checks if the same email has sent a message in the last 60 seconds
    $throttle_query = "SELECT id FROM contact_messages WHERE email = ? AND submitted_at > NOW() - INTERVAL 1 MINUTE";
    $stmt_check = $conn->prepare($throttle_query);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Spam Protection: Please wait at least 60 seconds between messages.'); window.history.back();</script>";
        exit();
    }
    $stmt_check->close();

    // 7. Secure Data Insertion using Prepared Statements (Prevents SQL Injection)
    $insert_query = "INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    
    if ($stmt) {
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        
        if ($stmt->execute()) {
            // Success Notification
            echo "<script>
                    alert('Submission Successful! Thank you, $name. We will contact you shortly.');
                    window.location.href='index.html';
                  </script>";
        } else {
            // Log execution error
            error_log("Query Execution Error: " . $stmt->error);
            echo "<script>alert('Server Error: Could not save your message.'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        error_log("Statement Preparation Failed: " . $conn->error);
        echo "<script>alert('Critical System Error. Please contact support.'); window.history.back();</script>";
    }
}

// 8. Close Resources
$conn->close();
?>