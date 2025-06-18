<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canteen Management System Login</title>
    <style>
        body { 
    background-image: url('images/welcome1.png');
    background-size: cover;
    background-position: center;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    font-family: Arial, sans-serif;
}

.login-container {
    background: rgba(255, 255, 255, 0.1); /* transparent white */
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    backdrop-filter: blur(10px); /* glass blur effect */
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    width: 600px;
    display: flex;
}

.login-section {
    flex: 1;
    padding: 20px;
}

.login-section h2 {
    text-align: center;
    font-size: 30px;
    margin-bottom: 20px;
    color:rgb(255, 255, 255);
}

.form-group {
    margin-bottom: 15px;
}

.form-group input {
    width: 96%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background: rgb(255, 255, 255);
    color: #000;
}

.login-button {
    width: 100%;
    padding: 10px;
    background-color: rgb(165, 75, 20);
    color: #ffffff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.login-button:hover {
    background-color: rgb(109, 58, 0);
}
p{
  color:rgb(221, 221, 221);
  
}
a {
  color:rgb(148, 148, 255);
  text-decoration: none;
}
</style>
</head>
<body>
    <div class="login-container">
        <div class="login-section">
            <h2>Customer Login</h2>
            <form action="customer_login.php" method="post">

                <div class="form-group">
                    <input type="text" id="name" name="name" placeholder="Name" required>
                </div>

                <div class="form-group">
                    
                    <input type="text" id="customer_id" name="customer_id" placeholder="College ID"required>
                </div>

                <div class="form-group">
                    
                    <input type="text" id="phno" name="phno" placeholder="phone" required>
                </div>

                <div class="form-group">
                    
                    <input type="password" id="password" name="password" placeholder="password"required>
                </div>
                <button class="login-button" type="submit">Login as user</button>
                <p>Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>
</body>
</html>
