<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include "db.php";

// Stats
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM medicines"))['total'];
$low = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS low_stock FROM medicines WHERE quantity < 10"))['low_stock'];
$expired = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS expired FROM medicines WHERE expiry_date < CURDATE()"))['expired'];

// Recent medicines
$medicines = mysqli_query($conn, "SELECT * FROM medicines ORDER BY id DESC LIMIT 10");

// Sales per month (example: sum total_amount per month)
$salesData = [];
$months = [];
$query = mysqli_query($conn, "SELECT MONTH(sale_date) AS month, SUM(total_amount) AS total
                               FROM sales 
                               WHERE YEAR(sale_date)=YEAR(CURDATE())
                               GROUP BY MONTH(sale_date)");
while($row = mysqli_fetch_assoc($query)){
    $months[] = date("F", mktime(0,0,0,$row['month'],10));
    $salesData[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy Admin Dashboard</title>
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f4f6f9;}
        /* Sidebar */
        .sidebar { position: fixed; left:0; top:0; width:220px; height:100%; background:#4CAF50; color:white; display:flex; flex-direction:column;}
        .sidebar h2 { text-align:center; padding:20px 0; margin:0; border-bottom:1px solid rgba(255,255,255,0.2);}
        .sidebar a { padding:15px 20px; text-decoration:none; color:white; display:block; transition:0.2s;}
        .sidebar a:hover { background:#45a049; }
        /* Main content */
        .main { margin-left:220px; padding:20px 40px;}
        header { background:white; padding:20px; border-radius:10px; margin-bottom:20px; box-shadow:0 3px 6px rgba(0,0,0,0.1);}
        .cards { display:flex; gap:20px; margin-bottom:30px; flex-wrap:wrap;}
        .card { background:white; flex:1; min-width:180px; padding:20px; border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,0.1); text-align:center; transition:0.2s; cursor:pointer; text-decoration:none; color:inherit;}
        .card:hover { transform:translateY(-5px); box-shadow:0 6px 12px rgba(0,0,0,0.2);}
        .card h2 { margin:0; font-size:28px; color:#4CAF50;}
        .card p { margin:5px 0 0 0; color:#555;}
        a.button { display:inline-block; margin:10px 10px 20px 0; padding:10px 20px; background:#4CAF50; color:white; text-decoration:none; border-radius:6px;}
        a.button:hover { background:#45a049;}
        input { padding:8px; margin:5px 0; width:250px;}
        table { width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden; box-shadow:0 3px 6px rgba(0,0,0,0.1);}
        th, td { padding:12px 15px; text-align:left; border-bottom:1px solid #ddd;}
        th { background:#4CAF50; color:white;}
        tr:nth-child(even) { background:#f9f9f9;}
        .charts { display:flex; gap:40px; flex-wrap:wrap; margin-top:30px;}
        .chart-container { background:white; padding:20px; border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,0.1); flex:1; min-width:300px;}
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="sidebar">
    <h2>Pharmacy Admin</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="view_medicines.php">View Medicines</a>
    <a href="add_medicine.php">Add Medicine</a>
    <a href="low_stock.php">Low Stock</a>
    <a href="expired.php">Expired Medicines</a>

    <a href="cart.php">Cart / Checkout</a>
    <a href="report.php">Reports</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <header>
        <h1>Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['admin']; ?></p>
    </header>

    <!-- Stats Cards -->
    <div class="cards">
        <a href="view_medicines.php" class="card">
            <h2><?php echo $total; ?></h2>
            <p>Total Medicines</p>
        </a>
        <a href="low_stock.php" class="card">
            <h2><?php echo $low; ?></h2>
            <p>Low Stock</p>
        </a>
        <a href="expired.php" class="card">
            <h2><?php echo $expired; ?></h2>
            <p>Expired Medicines</p>
        </a>
    </div>

    <!-- Action Buttons -->
    <a href="add_medicine.php" class="button">Add Medicine</a>
    <a href="sales.php" class="button">Sales</a>
    <a href="report.php" class="button">Print Report</a>

    <!-- Recent Medicines Table (Above Charts) -->
    <h2>Recent Medicines</h2>
    <input type="text" id="searchInput" placeholder="Search medicines..." onkeyup="filterTable()">
    <table id="medTable">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Batch No</th>
            <th>Expiry</th>
            <th>Quantity</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($medicines)) { ?>
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

    <!-- Charts Section -->
    <div class="charts">
        <div class="chart-container">
            <h3>Stock Overview</h3>
            <canvas id="stockChart"></canvas>
        </div>
        <div class="chart-container">
            <h3>Monthly Sales</h3>
            <canvas id="salesChart"></canvas>
        </div>
    </div>

</div>

<script>
// Table Search
function filterTable() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let table = document.getElementById("medTable");
    let tr = table.getElementsByTagName("tr");
    for (let i=1; i<tr.length; i++) {
        let tdName = tr[i].getElementsByTagName("td")[1];
        let tdCategory = tr[i].getElementsByTagName("td")[2];
        if(tdName){
            let text = tdName.innerText.toLowerCase() + " " + tdCategory.innerText.toLowerCase();
            tr[i].style.display = text.includes(input) ? "" : "none";
        }
    }
}

// Stock Overview Pie Chart
const ctxStock = document.getElementById('stockChart').getContext('2d');
const stockChart = new Chart(ctxStock, {
    type: 'pie',
    data: {
        labels: ['Low Stock','In Stock'],
        datasets: [{
            data: [<?php echo $low; ?>, <?php echo $total-$low; ?>],
            backgroundColor: ['#FF6384','#36A2EB']
        }]
    },
    options: { responsive:true }
});

// Monthly Sales Bar Chart
const ctxSales = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctxSales, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Sales (Ksh)',
            data: <?php echo json_encode($salesData); ?>,
            backgroundColor: '#4CAF50'
        }]
    },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } } }
});
</script>

</body>
</html>
