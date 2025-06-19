<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canteen Management System Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles/register.css">
    
</head>
<body>
    <header class="page-header">
        <a href="index.html" class="logo"> <img src="images/logo.png" alt="Canteen Express Logo"> </a>
    </header>

    <div class="main-content-wrapper">
        <div class="login-container">
            <div class="login-section">
                <h2>Register</h2>
                <form action="customer_login.php" method="post">
                    <div class="form-group">
                        <input type="text" id="name" name="name" placeholder="Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" id="customer_id" name="customer_id" placeholder="College ID" required>
                    </div>
                    <div class="form-group">
                        <input type="text" id="phno" name="phno" placeholder="Phone Number" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                    <button class="login-button" type="submit">Login as user</button>
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>