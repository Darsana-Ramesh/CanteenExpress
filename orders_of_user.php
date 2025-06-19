<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT o.*,m.item_price,m.item_name FROM orders o, menu m where o.item_id=m.item_id order by o.item_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CanteenExpress Orders Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles/order_admin.css">

</head>
<body>
    <header class="admin-header">
    <a href="adminhome.php" class="logo">
        <img src="images/logo.png" alt="Canteen Express Logo" class="logo-image">
    </a>
    <nav class="header-nav">
        <a href="adminhome.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</header>

    <div class="main-content">
        <h1 class="page-title">All Orders</h1> <div class="table-container"> <table>
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>Food ID</th>
                        <th>Food Name</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['customer_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['item_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td>₹ " . htmlspecialchars(number_format($row['quantity'] * $row['item_price'], 2)) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'><p class='no-data-message'>No orders found.</p></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>