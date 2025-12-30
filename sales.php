<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include "db.php";

// Initialize cart in session
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

// Add medicine to cart
if(isset($_POST['add_to_cart'])){
    $id = intval($_POST['medicine_id']);
    $qty = intval($_POST['quantity']);

    // Get medicine info
    $med = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM medicines WHERE id=$id"));

    if($med){
        // If already in cart, increase quantity
        if(isset($_SESSION['cart'][$id])){
            $_SESSION['cart'][$id]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $med['name'],
                'price' => $med['sell_price'],
                'quantity' => $qty
            ];
        }
    }
}

// Remove item from cart
if(isset($_GET['remove'])){
    $remove_id = intval($_GET['remove']);
    unset($_SESSION['cart'][$remove_id]);
}

// Clear cart after checkout
if(isset($_POST['checkout'])){
    // Reduce stock
    foreach($_SESSION['cart'] as $id => $item){
        mysqli_query($conn, "UPDATE medicines SET quantity = quantity - {$item['quantity']} WHERE id=$id");
    }

    // Save cart temporarily to session for receipt
    $_SESSION['last_cart'] = $_SESSION['cart'];

    // Clear cart
    $_SESSION['cart'] = [];

    // Redirect to receipt
    header("Location: receipt.php");
    exit();
}

// Fetch all medicines for search/select
$all_meds = mysqli_query($conn, "SELECT * FROM medicines ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales</title>
    <style>
        body { font-family: Arial; background: #f4f6f9; margin: 0; padding: 0; }
        header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
        .container { padding: 20px 40px; }
        select, input { padding: 8px; margin: 5px 0; }
        button { padding: 8px 15px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #45a049; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #4CAF50; color: white; }
        a { color: red; text-decoration: none; }
    </style>
</head>
<body>

<header>
    <h1>Sales</h1>
</header>

<div class="container">

<h2>Add Medicine to Cart</h2>
<form method="POST">
    <select name="medicine_id" required>
        <option value="">Select Medicine</option>
        <?php while($row = mysqli_fetch_assoc($all_meds)){ ?>
            <option value="<?php echo $row['id']; ?>">
                <?php echo $row['name'] . " (Stock: ".$row['quantity'].")"; ?>
            </option>
        <?php } ?>
    </select>
    <input type="number" name="quantity" placeholder="Quantity" required min="1">
    <button type="submit" name="add_to_cart">Add to Cart</button>
</form>

<h2>Cart</h2>
<?php if(!empty($_SESSION['cart'])){ ?>
<form method="POST">
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
    foreach($_SESSION['cart'] as $id => $item){ 
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
    ?>
    <tr>
        <td><?php echo $item['name']; ?></td>
        <td><?php echo number_format($item['price'],2); ?></td>
        <td><?php echo $item['quantity']; ?></td>
        <td><?php echo number_format($subtotal,2); ?></td>
        <td><a href="sales.php?remove=<?php echo $id; ?>">Remove</a></td>
    </tr>
    <?php } ?>
    <tr>
        <th colspan="3">Total</th>
        <th><?php echo number_format($total,2); ?></th>
        <th></th>
    </tr>
</table>
<br>
<button type="submit" name="checkout">Checkout</button>
</form>
<?php } else { ?>
<p>No items in cart.</p>
<?php } ?>

<br>
<a href="dashboard.php">Back to Dashboard</a>

</div>

</body>
</html>
