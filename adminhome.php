<?php
session_start();

if(!isset($_SESSION["username"]))
{
    header("location:login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CanteenExpress</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles/admin.css">
    
</head>
<body>
    <header>
    <div class="logo">
        <img src="images/logo.png" alt="Canteen Express Admin Logo" class="header-logo-img">
    </div>
    <div class="user-info">
        <span>Welcome, Admin!</span>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</header>

    <main>
        <div class="dashboard-card" id="workers">
            <div class="icon-container">
                <i class="fas fa-users-gear"></i> </div>
            <h2>Manage Workers</h2>
            <p>Add, edit, or remove canteen staff accounts and details.</p>
            <a href="workers.php" class="action-link">View Workers</a>
        </div>

        <div class="dashboard-card" id="menu">
            <div class="icon-container">
                <i class="fas fa-utensils"></i> </div>
            <h2>Canteen Menu</h2>
            <p>Update food items, prices, and categories available to customers.</p>
            <a href="menu.php" class="action-link">Manage Menu</a>
        </div>

        <div class="dashboard-card" id="orders">
            <div class="icon-container">
                <i class="fas fa-clipboard-list"></i> </div>
            <h2>Customer Orders</h2>
            <p>View and manage incoming customer orders and their status.</p>
            <a href="orders_of_user.php" class="action-link">View Orders</a>
        </div>

        <div class="dashboard-card" id="sales">
            <div class="icon-container">
                <i class="fas fa-chart-line"></i> </div>
            <h2>Sales Volume</h2>
            <p>Analyze sales data, track revenue, and monitor performance.</p>
            <a href="sales_volume.php" class="action-link">View Sales</a>
        </div>

        </main>
</body>
</html>