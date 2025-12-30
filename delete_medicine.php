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

mysqli_query($conn, "DELETE FROM medicines WHERE id=$id");

header("Location: view_medicines.php");
exit();
