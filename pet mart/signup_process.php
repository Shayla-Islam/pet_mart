<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ১. ইনপুট ডেটা সংগ্রহ ও স্যানিটাইজ করা
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // ২. পাসওয়ার্ড হ্যাশ করা (সিকিউরিটির জন্য)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ৩. ইউজার অলরেডি আছে কি না চেক করা
    $check_user = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $result = $conn->query($check_user);

    if ($result->num_rows > 0) {
        echo "<script>alert('Username or Email already exists!'); window.history.back();</script>";
    } else {
        // ৪. ডেটাবেসে ইনসার্ট করা
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            // ৫. অটোমেটিক লগইন সেশন তৈরি করা (এটিই প্রোফাইল দেখানোর মূল কাজ)
            $_SESSION['user_id'] = $conn->insert_id; // নতুন রেজিস্ট্রেশন হওয়া ইউজারের ID
            $_SESSION['username'] = $username;
            $_SESSION['loggedin'] = true;

            // ৬. সরাসরি প্রোফাইল পেজে পাঠিয়ে দেওয়া
            echo "<script>
                    alert('Registration Successful!');
                    window.location.href='profile.php';
                  </script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
$conn->close();
?>