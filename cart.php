<?php
session_start();
include "db.php";

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Initialize cart
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Handle adding to cart
if(isset($_POST['add_to_cart'])){
    $med_id = intval($_POST['medicine_id']);
    $qty = intval($_POST['quantity']);

    if($qty <= 0){
        $error = "Quantity must be at least 1";
    } else {
        if(isset($_SESSION['cart'][$med_id])){
            $_SESSION['cart'][$med_id] += $qty;
        } else {
            $_SESSION['cart'][$med_id] = $qty;
        }
        $success = "Medicine added to cart!";
    }
}

// Remove item from cart
if(isset($_GET['remove'])){
    $remove_id = intval($_GET['remove']);
    unset($_SESSION['cart'][$remove_id]);
}

// Handle search
$search = "";
if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $medicines = mysqli_query($conn, "SELECT * FROM medicines WHERE name LIKE '%$search%' ORDER BY name ASC");
} else {
    $medicines = mysqli_query($conn, "SELECT * FROM medicines ORDER BY name ASC");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart - Pharmacy System</title>
    <style>
        body { font-family: Arial, sans-serif; background:#ecf0f1; margin:0; padding:0; }
        .sidebar { width:200px; background:#2c3e50; height:100vh; float:left; padding-top:20px; }
        .sidebar h2 { color:white; text-align:center; margin-bottom:30px; }
        .sidebar a { display:block; color:white; padding:12px; text-decoration:none; margin:5px 0; transition:0.3s; }
        .sidebar a:hover { background:#34495e; }
        .main-content { margin-left:210px; padding:20px; }
        h2,h3 { color:#2c3e50; }
        table { width:100%; border-collapse:collapse; margin-bottom:20px; }
        table th, table td { padding:10px; text-align:left; border-bottom:1px solid #ddd; }
        table th { background:#2980b9; color:white; }
        table tr:nth-child(even){ background:#f4f6f9; }
        input[type=text], input[type=number] { padding:8px; width:90%; margin:5px 0; border:1px solid #ccc; border-radius:4px; }
        button { padding:8px 15px; background:#27ae60; color:white; border:none; border-radius:4px; cursor:pointer; transition:0.3s; }
        button:hover { background:#219150; }
        .success { color:green; margin:10px 0; }
        .error { color:red; margin:10px 0; }
        .search-form { margin-bottom:20px; }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>Pharmacy System</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="add_medicine.php">Add Medicine</a>
    <a href="view_medicines.php">View Medicines</a>
    <a href="cart.php">Cart / Checkout</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
<h2>Add Medicine to Cart</h2>

<?php 
if(isset($success)) echo "<p class='success'>$success</p>";
if(isset($error)) echo "<p class='error'>$error</p>";
?>

<!-- Search form -->
<form method="GET" class="search-form">
    <input type="text" name="search" placeholder="Search medicines..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<h3>Select Medicine</h3>
<form method="POST">
<table>
<tr>
    <th>Medicine</th>
    <th>Price (Ksh)</th>
    <th>Available</th>
    <th>Quantity</th>
    <th>Action</th>
</tr>
<?php while($row = mysqli_fetch_assoc($medicines)) { ?>
<tr>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo number_format($row['sell_price'],2); ?></td>
    <td><?php echo $row['quantity']; ?></td>
    <td>
        <input type="hidden" name="medicine_id" value="<?php echo $row['id']; ?>">
        <input type="number" name="quantity" min="1" max="<?php echo $row['quantity']; ?>" value="1">
    </td>
    <td><button name="add_to_cart">Add to Cart</button></td>
</tr>
<?php } ?>
</table>
</form>

<h3>Cart</h3>
<table>
<tr>
    <th>Medicine</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
    <th>Action</th>
</tr>
<?php
$total = 0;
foreach($_SESSION['cart'] as $med_id => $qty){
    $med = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM medicines WHERE id=$med_id"));
    $subtotal = $med['sell_price'] * $qty;
    $total += $subtotal;
?>
<tr>
    <td><?php echo htmlspecialchars($med['name']); ?></td>
    <td><?php echo number_format($med['sell_price'],2); ?></td>
    <td><?php echo $qty; ?></td>
    <td><?php echo number_format($subtotal,2); ?></td>
    <td><a href="?remove=<?php echo $med_id; ?>">Remove</a></td>
</tr>
<?php } ?>
<tr>
    <td colspan="3" style="text-align:right;"><strong>Total</strong></td>
    <td><?php echo number_format($total,2); ?></td>
    <td></td>
</tr>
</table>

<?php if(!empty($_SESSION['cart'])){ ?>
<form action="checkout.php" method="POST" class="checkout-form">
    <input type="text" name="customer_name" placeholder="Customer Name" required>
    <button type="submit" name="checkout">Checkout</button>
</form>
<?php } ?>

</div>
</body>
</html>
