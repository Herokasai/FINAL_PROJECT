<?php
session_start();
include "db.php";

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Insert medicine
if(isset($_POST['add'])){
    $name = $_POST['name'];
    $category = $_POST['category'];
    $batch = $_POST['batch_no'];
    $expiry = $_POST['expiry_date'];
    $buy_price = $_POST['buy_price'];
    $sell_price = $_POST['sell_price'];
    $quantity = $_POST['quantity'];

    $query = mysqli_query($conn, "INSERT INTO medicines 
        (name, category, batch_no, expiry_date, buy_price, sell_price, quantity)
        VALUES ('$name','$category','$batch','$expiry','$buy_price','$sell_price','$quantity')");

    if($query){
        $success = "Medicine added successfully!";
    } else {
        $error = "Failed to add medicine: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Medicine</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        /* Form container */
        .form-container {
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            width: 400px;
        }

        /* Form title */
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
        }

        /* Labels and inputs */
        label {
            font-weight: 500;
            margin-top: 10px;
            display: block;
            color: #555;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        /* Button */
        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        button:hover {
            background: #45a049;
        }

        /* Success & error messages */
        p.success {
            color: green;
            margin-top: 15px;
            text-align: center;
            font-weight: 500;
        }

        p.error {
            color: red;
            margin-top: 15px;
            text-align: center;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add Medicine</h2>

    <form method="POST">
        <label>Medicine Name</label>
        <input type="text" name="name" required placeholder="e.g., Paracetamol 500mg">

        <label>Category</label>
        <input type="text" name="category" required placeholder="e.g., Painkiller">

        <label>Batch Number</label>
        <input type="text" name="batch_no" required placeholder="e.g., B001">

        <label>Expiry Date (YYYY-MM-DD)</label>
        <input type="date" name="expiry_date" required>

        <label>Buying Price (Ksh)</label>
        <input type="number" name="buy_price" required placeholder="e.g., 50">

        <label>Selling Price (Ksh)</label>
        <input type="number" name="sell_price" required placeholder="e.g., 70">

        <label>Quantity</label>
        <input type="number" name="quantity" required placeholder="e.g., 100">

        <button name="add">Add Medicine</button>

        <?php 
        if(isset($success)) echo "<p class='success'>$success</p>";
        if(isset($error)) echo "<p class='error'>$error</p>";
        ?>
    </form>
</div>

</body>
</html>
