<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total_bill = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Shopping Cart | PetMart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h3 class="logo"><a href="index.html">PetMart</a></h3>
    </header>

    <section style="padding: 150px 8% 50px;">
        <h2>Your Saved Cart (PHP Database)</h2>
        <table style="width:100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background:#eee;">
                    <th style="padding:15px; text-align:left;">Product</th>
                    <th style="padding:15px; text-align:left;">Price</th>
                    <th style="padding:15px; text-align:left;">Quantity</th>
                    <th style="padding:15px; text-align:left;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $result->fetch_assoc()): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total_bill += $subtotal;
                ?>
                <tr>
                    <td style="padding:15px; border-bottom:1px solid #ddd;">
                        <img src="<?php echo $item['image_path']; ?>" width="50"> <?php echo $item['product_name']; ?>
                    </td>
                    <td style="padding:15px; border-bottom:1px solid #ddd;">৳<?php echo $item['price']; ?></td>
                    <td style="padding:15px; border-bottom:1px solid #ddd;"><?php echo $item['quantity']; ?></td>
                    <td style="padding:15px; border-bottom:1px solid #ddd;">৳<?php echo $subtotal; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div style="text-align: right; margin-top: 30px;">
            <h3>Grand Total: ৳<span id="totalPrice"><?php echo $total_bill; ?></span></h3>
            <br>
            <button class="main-btn">Proceed to Checkout</button>
        </div>
    </section>
</body>
</html>