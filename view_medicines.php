<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include "db.php";

$medicines = mysqli_query($conn, "SELECT * FROM medicines ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Medicines</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin:0; padding:20px; }
        h2 { text-align:center; margin-bottom:20px; color:#333; }

        table {
            width: 100%;
            border-collapse: collapse;
            background:white;
            border-radius:8px;
            overflow:hidden;
            box-shadow:0 3px 6px rgba(0,0,0,0.1);
        }

        th, td {
            padding:12px 15px;
            text-align:left;
            border-bottom:1px solid #ddd;
        }

        th {
            background:#4CAF50;
            color:white;
        }

        tr:nth-child(even) { background:#f9f9f9; }
        tr:hover { background:#f1f1f1; }

        /* Action links */
        a {
            text-decoration:none;
            padding:5px 10px;
            border-radius:5px;
            transition:0.2s;
            font-size:14px;
        }

        a:hover { opacity:0.8; }

        a.edit { background:#2196F3; color:white; }
        a.delete { background:#f44336; color:white; }

        /* Back button */
        .back-btn {
            display:inline-block;
            margin-top:20px;
            padding:10px 20px;
            background:#4CAF50;
            color:white;
            border-radius:6px;
            text-decoration:none;
            transition:0.2s;
        }

        .back-btn:hover { background:#45a049; }
    </style>
</head>
<body>

<h2>All Medicines</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Category</th>
        <th>Batch No</th>
        <th>Expiry Date</th>
        <th>Buy Price</th>
        <th>Sell Price</th>
        <th>Quantity</th>
        <th>Actions</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($medicines)) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['category']; ?></td>
        <td><?php echo $row['batch_no']; ?></td>
        <td><?php echo $row['expiry_date']; ?></td>
        <td><?php echo $row['buy_price']; ?></td>
        <td><?php echo $row['sell_price']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td>
            <a class="edit" href="edit_medicine.php?id=<?php echo $row['id']; ?>">Edit</a>
            <a class="delete" href="delete_medicine.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>

<a class="back-btn" href="dashboard.php">Back to Dashboard</a>

</body>
</html>
