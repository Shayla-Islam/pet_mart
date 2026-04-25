<?php
$host = "localhost";
$user = "root";      // XAMPP-এর ডিফল্ট ইউজার 'root'
$pass = "";          // XAMPP-এর ডিফল্ট পাসওয়ার্ড ফাঁকা থাকে
$dbname = "petmart_db"; // আপনার ডাটাবেস এর নাম

// কানেকশন তৈরি
$conn = mysqli_connect($host, $user, $pass, $dbname);

// চেক করা কানেকশন কাজ করছে কি না
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>