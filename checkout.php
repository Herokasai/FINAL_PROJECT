<?php
session_start();
include "db.php";

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

if(empty($_SESSION['cart'])){
    die("No items in cart!");
}

$customer = mysqli_real_escape_string($conn, $_POST['customer_name']);
$total = 0;

// Calculate total
foreach($_SESSION['cart'] as $med_id => $qty){
    $med = mysqli_fetch_assoc(mysqli_query($conn, "SELECT sell_price FROM medicines WHERE id=$med_id"));
    $total += $med['sell_price'] * $qty;
}

// Insert order
mysqli_query($conn, "INSERT INTO orders (customer_name, total) VALUES ('$customer','$total')");
$order_id = mysqli_insert_id($conn);

// Insert order items + reduce stock
foreach($_SESSION['cart'] as $med_id => $qty){
    $med = mysqli_fetch_assoc(mysqli_query($conn, "SELECT sell_price, quantity FROM medicines WHERE id=$med_id"));
    $price = $med['sell_price'];
    $new_qty = $med['quantity'] - $qty;

    mysqli_query($conn, "INSERT INTO order_items (order_id, medicine_id, quantity, price)
                          VALUES ($order_id, $med_id, $qty, $price)");

    mysqli_query($conn, "UPDATE medicines SET quantity=$new_qty WHERE id=$med_id");
}

// Clear cart
unset($_SESSION['cart']);

// Redirect to receipt
header("Location: receipt.php?order_id=$order_id");
exit();
