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
    
    if ($row["usertype"] == "user") {
        $_SESSION["username"] = $username;
        header("location:userhome.php");
    } elseif ($row["usertype"] == "admin") {
        $_SESSION["username"] = $username;
        header("location:adminhome.php");
    } else {
        echo "username or password incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>GEC Canteen Login Page</title>
  <style>
    body {
  background-color: #f4f4f4;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-image: url('images/welcome1.png'); /* Corrected */
  background-size: cover;
  background-position: center;
  font-family: Arial, sans-serif;
}

form {
  background: rgba(255, 255, 255, 0.1); /* Transparent white background */
  backdrop-filter: blur(10px); /* Glass effect */
  -webkit-backdrop-filter: blur(10px);
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
  width: 100%;
  max-width: 400px;
  border: 1px solid rgba(255, 255, 255, 0.18);
}

input[type="text"],
input[type="password"] {
  width: 100%;
  padding: 10px;
  margin: 5px 0;
  box-sizing: border-box;
  border: 1px solid #ccc;
  border-radius: 3px;
  background: rgb(255, 255, 255);
  color: #000;
}
p{
  color:rgb(221, 221, 221);
  
}
input[type="submit"] {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  background-color:rgb(165, 75, 20);
  color: #fff;
  border: none;
  border-radius: 3px;
  cursor: pointer;
}

input[type="submit"]:hover {
  background-color:rgb(109, 58, 0);
}

h1 {
  color:rgb(255, 255, 255);
}

a {
  color:rgb(148, 148, 255);
  text-decoration: none;
}


  </style>
</head>
<body>
  <form action="#" method="POST">
    <center><h1>Login</h1></center>
    <input type="text" id="username" name="username" placeholder="College ID" required>
    <input type="password" id="password" name="password" placeholder="password" required>
    <input type="submit" value="Login">
    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
  </form>
</body>
</html>
