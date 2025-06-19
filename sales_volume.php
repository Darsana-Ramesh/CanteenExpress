<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query for total quantities of items in orders
$sql_quantities = "SELECT item_id, sum(quantity) as q FROM orders GROUP BY item_id ORDER BY item_id";
$result_quantities = $conn->query($sql_quantities);

// Query for item names and prices for items that have been ordered
$sql_details = "SELECT item_id, item_name, item_price FROM menu WHERE item_id IN (SELECT item_id FROM orders) ORDER BY item_id";
$result_details = $conn->query($sql_details);

// Initialize an array to hold combined sales data
$sales_data = [];

// 1. Populate sales_data with quantities from orders
if ($result_quantities && $result_quantities->num_rows > 0) {
    while ($row = $result_quantities->fetch_assoc()) {
        // Ensure 'item_id' exists before using it as a key
        if (isset($row['item_id'])) {
            $sales_data[$row['item_id']]['quantity'] = $row['q'] ?? 0;
            // Initialize other fields to prevent undefined key warnings if no matching menu item
            $sales_data[$row['item_id']]['item_name'] = 'N/A';
            $sales_data[$row['item_id']]['item_price'] = 0.00;
        }
    }
}

// 2. Add item names and prices to sales_data
if ($result_details && $result_details->num_rows > 0) {
    while ($row1 = $result_details->fetch_assoc()) {
        // Ensure 'item_id' exists before using it as a key
        if (isset($row1['item_id'])) {
            // Ensure the main item_id entry exists, initialize if it only appears in menu
            if (!isset($sales_data[$row1['item_id']])) {
                $sales_data[$row1['item_id']] = ['quantity' => 0]; // Default quantity if menu item has no orders
            }
            $sales_data[$row1['item_id']]['item_name'] = $row1['item_name'] ?? 'N/A';
            $sales_data[$row1['item_id']]['item_price'] = $row1['item_price'] ?? 0.00;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CanteenExpress Sales Volume</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles/sales_volume.css">
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
        <h1 class="page-title">Sales Volume</h1>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ITEM ID</th>
                        <th>ITEM NAME</th>
                        <th>ITEM PRICE</th>
                        <th>TOTAL COUNT</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if sales data was collected
                    if (!empty($sales_data)) {
                        // Sort sales_data by item_id for consistent display
                        ksort($sales_data);

                        foreach ($sales_data as $item_id => $data) {
                            $item_name = htmlspecialchars($data['item_name'] ?? 'N/A');
                            $item_price = $data['item_price'] ?? 0.00; // Keep as number for calculation
                            $quantity = $data['quantity'] ?? 0;        // Keep as number for calculation
                            $total_amount = $quantity * $item_price;

                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($item_id) . "</td>";
                            echo "<td>" . $item_name . "</td>";
                            echo "<td>₹ " . number_format($item_price, 2) . "</td>";
                            echo "<td>" . htmlspecialchars($quantity) . "</td>";
                            echo "<td>₹ " . number_format($total_amount, 2) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'><p class='no-data-message'>No sales data found.</p></td></tr>";
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