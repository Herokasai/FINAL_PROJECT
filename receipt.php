<?php
session_start();
include "db.php";

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['order_id']) || empty($_GET['order_id'])){
    die("Invalid order! No order selected.");
}

$order_id = intval($_GET['order_id']);

// Fetch order
$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id=$order_id"));
if(!$order) die("Order not found!");

// Fetch order items
$result = mysqli_query($conn, "SELECT m.name, m.sell_price, oi.quantity 
    FROM order_items oi
    JOIN medicines m ON m.id=oi.medicine_id
    WHERE oi.order_id=$order_id");

$total = 0;
$order_items = [];
while($row = mysqli_fetch_assoc($result)){
    $subtotal = $row['sell_price'] * $row['quantity'];
    $total += $subtotal;
    $row['subtotal'] = $subtotal;
    $order_items[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt - Pharmacy System</title>
    <style>
        body { font-family: Arial, sans-serif; background:#ecf0f1; padding:20px; }
        .main-content { max-width:800px; margin: auto; background:white; padding:30px; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.1);}
        h2 { text-align:center; margin-bottom:20px; color:#2c3e50; }
        p { margin:5px 0; font-size:16px; }
        table { width:100%; border-collapse:collapse; margin-top:20px; margin-bottom:20px; }
        table th, table td { padding:12px; text-align:left; border-bottom:1px solid #ddd; }
        table th { background-color:#2980b9; color:white; }
        table tr:nth-child(even) { background:#f4f6f9; }
        table tr:last-child td { font-weight:bold; border-bottom:none; }
        button { background-color:#27ae60; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer; transition:0.3s; margin-top:10px; }
        button:hover { background-color:#219150; }
        a { display:inline-block; margin-top:15px; text-decoration:none; color:#2980b9; font-weight:bold; }
        a:hover { text-decoration:underline; }
    </style>
</head>
<body>
<div class="main-content">
    <h2>Receipt</h2>
    <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
    <p><strong>Date:</strong> <?php echo $order['created_at']; ?></p>

    <table>
        <tr>
            <th>Medicine</th>
            <th>Price (Ksh)</th>
            <th>Quantity</th>
            <th>Subtotal (Ksh)</th>
        </tr>
        <?php foreach($order_items as $item){ ?>
        <tr>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><?php echo number_format($item['sell_price'],2); ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td><?php echo number_format($item['subtotal'],2); ?></td>
        </tr>
        <?php } ?>
        <tr>
            <td colspan="3" style="text-align:right;">Total</td>
            <td><?php echo number_format($total,2); ?></td>
        </tr>
    </table>

    <button onclick="window.print()">Print Receipt</button>
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>
