<?php
session_start();
if(!isset($_SESSION['admin'])) header("Location: login.php");
include "db.php";

$meds = mysqli_query($conn, "SELECT * FROM medicines ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medicines Report</title>
    <style>
        body{ font-family: Arial; padding: 20px;}
        table{ width: 100%; border-collapse: collapse; margin-top: 20px;}
        th, td{ padding: 10px; border: 1px solid #000; text-align: left;}
        th{ background: #4CAF50; color: white;}
        @media print{
            a{ display:none; }
        }
    </style>
</head>
<body>

<h1>Medicines Report</h1>
<a href="#" onclick="window.print()">Print Report</a>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Category</th>
        <th>Batch No</th>
        <th>Expiry</th>
        <th>Quantity</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($meds)) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['category']; ?></td>
        <td><?php echo $row['batch_no']; ?></td>
        <td><?php echo $row['expiry_date']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
