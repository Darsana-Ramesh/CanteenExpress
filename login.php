<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "user";

session_start();
$data = mysqli_connect($host, $user, $password, $db);

if ($data === false) {
    die("connection error");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $sql = "SELECT * FROM login WHERE username='" . $username . "' AND password='" . $password . "' ";
    $result = mysqli_query($data, $sql);
    $row = mysqli_fetch_array($result);

    if (is_array($row)) { 
        if ($row["usertype"] == "user") {
            $_SESSION["username"] = $username;
            header("location:userhome.php");
            exit(); 
        } elseif ($row["usertype"] == "admin") {
            $_SESSION["username"] = $username;
            header("location:adminhome.php");
            exit(); 
        }
    }

    echo "<script>alert('Username or password incorrect');</script>"; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Canteen Express Login Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles/login.css">

    
</head>
<body>
    <header class="login-header">
        <div class="logo">
            <img src="images/logo.png" alt="Canteen Express Logo"> </div>
        </header>

    <div class="login-container">
        <form action="#" method="POST">
            <center><h1>Login</h1></center>
            <input type="text" id="username" name="username" placeholder="College ID" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
            <p>Don't have an account? <a href="signup.php">Register</a></p>
        </form>
    </div>
</body>
</html>