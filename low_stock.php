<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include "db.php";

// Fetch low stock medicines
$low_meds = mysqli_query($conn, "SELECT * FROM medicines WHERE quantity < 10 ORDER BY quantity ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Medicines</title>
    <style>
        body { font-family: Arial; background: #f4f6f9; padding: 20px; }
        h1 { text-align: center; color: #d9534f; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 3px 6px rgba(0,0,0,0.1);}
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #d9534f; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        a.button { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 6px; }
        a.button:hover { background: #45a049; }
    </style>
</head>
<body>

<h1>Low Stock Medicines (Qty < 10)</h1>

<?php if(mysqli_num_rows($low_meds) > 0){ ?>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Category</th>
        <th>Batch No</th>
        <th>Quantity</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($low_meds)){ ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['category']; ?></td>
        <td><?php echo $row['batch_no']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
    </tr>
    <?php } ?>
</table>
<?php } else { ?>
<p style="text-align:center;">No medicines are low in stock.</p>
<?php } ?>

<a href="dashboard.php" class="button">Back to Dashboard</a>

</body>
</html>
