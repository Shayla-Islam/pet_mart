<?php
// 1. Start Session
session_start();

// 2. Include Database Connection
include 'db_connect.php';

// 3. Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 4. Sanitize Inputs
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // 5. Validation
    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill in all fields!'); window.location='login.html';</script>";
        exit();
    }

    // 6. Database Query (Search by Username)
    $query = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // 7. Verify Hashed Password
        if (password_verify($password, $row['password'])) {
            // 8. Success: Set Session Variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['loggedin'] = true;

            // Redirect to Profile Page as requested
            header("Location: profile.php");
            exit();
        } else {
            // Invalid Password
            echo "<script>alert('Invalid Password!'); window.location='login.html';</script>";
            exit();
        }
    } else {
        // User not found
        echo "<script>alert('User not found!'); window.location='login.html';</script>";
        exit();
    }
}

$conn->close();
?>