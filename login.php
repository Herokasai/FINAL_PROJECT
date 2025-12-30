<?php
session_start();
require_once "db.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM admins 
            WHERE username='$username' AND password='$password' 
            LIMIT 1";

    $query = mysqli_query($conn, $sql);

    if ($query && mysqli_num_rows($query) === 1) {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy Admin Login</title>
    <style>
        /* Body styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Login card */
        .login-card {
            background: #fff;
            padding: 50px 40px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            width: 360px;
            position: relative;
        }

        /* Logo placeholder */
        .login-card .logo {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: bold;
            color: #6c5ce7;
        }

        /* Form group */
        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        /* Inputs */
        input {
            width: 100%;
            padding: 14px 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            background: none;
        }

        input:focus {
            outline: none;
            border-color: #6c5ce7;
            box-shadow: 0 0 5px #6c5ce7;
        }

        /* Floating labels */
        label {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            background: white;
            padding: 0 5px;
            color: #aaa;
            font-size: 16px;
            transition: 0.3s;
            pointer-events: none;
        }

        input:focus + label,
        input:not(:placeholder-shown) + label {
            top: -10px;
            font-size: 12px;
            color: #6c5ce7;
        }

        /* Submit button */
        button {
            width: 100%;
            padding: 14px;
            background-color: #6c5ce7;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #341f97;
        }

        /* Error message */
        .error-msg {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }

        /* Responsive */
        @media(max-width: 400px){
            .login-card {
                width: 90%;
                padding: 40px 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo">Pharmacy Admin</div>

    <?php if(isset($error)) { ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <div class="form-group">
            <input type="text" name="username" placeholder=" " required>
            <label>Username</label>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder=" " required>
            <label>Password</label>
        </div>
        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>
