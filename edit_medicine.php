<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include "db.php";

if(!isset($_GET['id'])){
    header("Location: view_medicines.php");
    exit();
}

$id = intval($_GET['id']);

$med = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM medicines WHERE id=$id"));

if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $batch = mysqli_real_escape_string($conn, $_POST['batch']);
    $expiry = $_POST['expiry'];
    $buy = $_POST['buy'];
    $sell = $_POST['sell'];
    $qty = $_POST['qty'];

    $update = "UPDATE medicines SET 
        name='$name', category='$category', batch_no='$batch', 
        expiry_date='$expiry', buy_price='$buy', sell_price='$sell', quantity='$qty'
        WHERE id=$id";

    if(mysqli_query($conn, $update)){
        $msg = "Medicine updated successfully!";
        $med = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM medicines WHERE id=$id"));
    } else {
        $msg = "Error: ".mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Medicine</title>
</head>
<body>

<h2>Edit Medicine</h2>

<?php if(isset($msg)) echo "<p>$msg</p>"; ?>

<form method="POST">
    <input type="text" name="name" value="<?php echo $med['name']; ?>" required><br><br>
    <input type="text" name="category" value="<?php echo $med['category']; ?>" required><br><br>
    <input type="text" name="batch" value="<?php echo $med['batch_no']; ?>" required><br><br>
    <input type="date" name="expiry" value="<?php echo $med['expiry_date']; ?>" required><br><br>
    <input type="number" step="0.01" name="buy" value="<?php echo $med['buy_price']; ?>" required><br><br>
    <input type="number" step="0.01" name="sell" value="<?php echo $med['sell_price']; ?>" required><br><br>
    <input type="number" name="qty" value="<?php echo $med['quantity']; ?>" required><br><br>
    <button type="submit" name="update">Update Medicine</button>
</form>

<br>
<a href="view_medicines.php">Back to Medicines</a>

</body>
</html>
