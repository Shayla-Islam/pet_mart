<?php
// 1. Start Session and check if user is logged in
session_start();

// Security: If not logged in, redirect to login page immediately
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit();
}

// 2. Include Database Connection
include 'db_connect.php';

// 3. Fetch User Data based on Session (using user_id is more secure than username)
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

// 4. Fetch Recent Contact Messages sent by this specific user email
$msg_query = "SELECT * FROM contact_messages WHERE email = ? ORDER BY submitted_at DESC LIMIT 3";
$stmt_msg = $conn->prepare($msg_query);
$stmt_msg->bind_param("s", $user_data['email']);
$stmt_msg->execute();
$recent_messages = $stmt_msg->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | PetMart</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body { background: #f9f9f9; }
        .profile-wrapper { padding: 150px 8% 50px; display: flex; gap: 30px; flex-wrap: wrap; }
        .user-card { flex: 1; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; min-width: 300px; }
        .user-card i { font-size: 100px; color: #ee1c47; }
        .activity-card { flex: 2; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); min-width: 300px; }
        .logout-btn { background: #ee1c47; color: white; padding: 10px 25px; border-radius: 5px; display: inline-block; margin-top: 20px; transition: 0.3s; text-decoration: none; border: none; cursor: pointer; }
        .logout-btn:hover { background: #222; color: white; }
        .msg-item { border-bottom: 1px solid #eee; padding: 15px 0; }
        .msg-item:last-child { border-bottom: none; }
        header { position: fixed; width: 100%; top: 0; right: 0; z-index: 1000; display: flex; align-items: center; justify-content: space-between; background: #fff; padding: 15px 8%; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<header>
    <h3 class="logo"><a href="index.html" style="color: #222; text-decoration: none;">PetMart</a></h3>
    <ul class="menu" style="display: flex; list-style: none; gap: 20px;">
        <li><a href="index.html" style="text-decoration: none; color: #222;">Home</a></li>
        <li><a href="cart.php" style="text-decoration: none; color: #222;">My Cart</a></li>
    </ul>
    <div class="nav-icon">
        <a href="logout.php" class="logout-btn" style="padding: 8px 18px; margin: 0; font-size: 14px;">Logout</a>
    </div>
</header>

<section class="profile-wrapper">
    <div class="user-card">
        <i class='bx bxs-user-circle'></i>
        <h2 style="margin-top:15px;"><?php echo htmlspecialchars($user_data['username']); ?></h2>
        <p style="color: #666;"><?php echo htmlspecialchars($user_data['email']); ?></p>
        <p style="color: #888; font-size: 14px; margin-top: 5px;">Phone: <?php echo htmlspecialchars($user_data['phone'] ?? 'N/A'); ?></p>
        
        <hr style="margin: 25px 0; border: 0; border-top: 1px solid #eee;">
        
        <p><strong>Account Status:</strong> <span style="color: green;">Verified Member</span></p>
        <p style="font-size: 13px; color: #999; margin-top: 10px;">Joined: <?php echo date('M d, Y', strtotime($user_data['created_at'])); ?></p>
        
        <a href="logout.php" class="logout-btn">Sign Out</a>
    </div>

    <div class="activity-card">
        <h3><i class='bx bx-history'></i> Your Recent Inquiries</h3>
        <div style="margin-top: 20px;">
            <?php if ($recent_messages && $recent_messages->num_rows > 0): ?>
                <?php while($msg = $recent_messages->fetch_assoc()): ?>
                    <div class="msg-item">
                        <p style="color: #ee1c47; font-weight: 600;">Subject: <?php echo htmlspecialchars($msg['subject']); ?></p>
                        <p style="font-size: 14px; color: #444; margin: 5px 0;"><?php echo htmlspecialchars($msg['message']); ?></p>
                        <small style="color: #999;">Sent on: <?php echo date('F j, Y, g:i a', strtotime($msg['submitted_at'])); ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 40px 0;">
                    <i class='bx bx-message-rounded-dots' style="font-size: 50px; color: #ccc;"></i>
                    <p style="color: #999; margin-top: 10px;">You haven't sent any messages yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

</body>
</html>
<?php 
$stmt->close();
$conn->close(); 
?>