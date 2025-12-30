<?php
session_start();
include "db.php";

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Quick stats
$total_medicines = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM medicines"))['total'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"))['total'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) AS total FROM orders"))['total'];

// Fetch recent 5 orders
$recent_orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy System - Home</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; padding:0; background:#ecf0f1; }
        .sidebar { width:220px; background:#2c3e50; height:100vh; float:left; padding-top:30px; }
        .sidebar h2 { color:white; text-align:center; margin-bottom:40px; font-size:22px; }
        .sidebar a { display:block; color:white; padding:12px; text-decoration:none; margin:8px 0; transition:0.3s; font-weight:bold; }
        .sidebar a:hover { background:#34495e; }
        .main { margin-left:230px; padding:30px; }
        h1 { color:#2c3e50; }
        .cards { display:flex; gap:20px; margin-top:20px; flex-wrap:wrap; }
        .card { background:white; padding:20px; border-radius:12px; box-shadow:0 4px 8px rgba(0,0,0,0.1); flex:1; min-width:200px; text-align:center; }
        .card h3 { color:#2980b9; margin-bottom:10px; }
        .card p { font-size:24px; margin:0; color:#2c3e50; }
        a.button { display:inline-block; background:#2980b9; color:white; padding:12px 20px; border-radius:8px; text-decoration:none; margin-top:20px; transition:0.3s; }
        a.button:hover { background:#1c5980; }
        table { width:100%; border-collapse:collapse; margin-top:30px; background:white; border-radius:8px; overflow:hidden; box-shadow:0 3px 6px rgba(0,0,0,0.1); }
        table th, table td { padding:12px; text-align:left; border-bottom:1px solid #ddd; }
        table th { background:#2980b9; color:white; }
        table tr:nth-child(even){ background:#f4f6f9; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Pharmacy System</h2>
    <a href="index.php">Dashboard</a>
    <a href="add_medicine.php">Add Medicine</a>
    <a href="view_medicines.php">View Medicines</a>
    <a href="cart.php">Cart / Checkout</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <h1>Welcome, <?php echo $_SESSION['admin']; ?>!</h1>

    <div class="cards">
        <div class="card">
            <h3>Total Medicines</h3>
            <p><?php echo $total_medicines; ?></p>
            <a href="view_medicines.php" class="button">View Medicines</a>
        </div>
        <div class="card">
            <h3>Total Orders</h3>
            <p><?php echo $total_orders; ?></p>
            <a href="cart.php" class="button">View Orders / Cart</a>
        </div>
        <div class="card">
            <h3>Total Revenue (Ksh)</h3>
            <p><?php echo number_format($total_revenue,2); ?></p>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <h2>Recent Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Total (Ksh)</th>
            <th>Date</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($recent_orders)){ ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
            <td><?php echo number_format($row['total'],2); ?></td>
            <td><?php echo $row['created_at']; ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
