<?php
// Start session if you need it for other purposes, but not strictly for this order processing
// session_start(); 

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// PHP Logic Adjustment to handle multiple items from the dynamic form
$customer_id = $_POST['customer_id'] ?? ''; // Use null coalescing operator for robustness
// 'items' will be an associative array: [item_id => quantity] for the selected items
$items_ordered = isset($_POST['items']) ? $_POST['items'] : []; 

$all_orders_successful = true;
$ordered_items_count = 0;

if (!empty($customer_id) && is_array($items_ordered) && !empty($items_ordered)) {
    foreach ($items_ordered as $item_id => $quantity) {
        // Only process items that have a quantity greater than 0
        // The JavaScript on userhome.php now only sends items with quantity > 0
        // so the 'in_array($_POST['selected_items'])' check is no longer needed.
        if ($quantity > 0) {
            // Sanitize inputs to prevent SQL injection (IMPORTANT for real applications)
            // For production, prepared statements are strongly recommended.
            $customer_id_safe = $conn->real_escape_string($customer_id);
            $item_id_safe = $conn->real_escape_string($item_id);
            $quantity_safe = $conn->real_escape_string($quantity);

            $sql = "INSERT INTO orders(customer_id, item_id, quantity) VALUES ('$customer_id_safe', '$item_id_safe', '$quantity_safe')";

            if ($conn->query($sql) === TRUE) {
                $ordered_items_count++;
            } else {
                $all_orders_successful = false;
                // Log the error but continue trying other orders if possible
                error_log("Error inserting order for item $item_id: " . $conn->error);
            }
        }
    }
} else {
    $all_orders_successful = false; // No customer ID, no items, or empty items array submitted
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="styles/order_success.css" rel="stylesheet">
    
</head>
<body>
    <div class="message-box">
        <?php
        if ($all_orders_successful && $ordered_items_count > 0) {
            echo '<p class="success-message">Order placed successfully!</p>';
            echo '<p style="font-size:1.1em; color:#555;">You ordered ' . $ordered_items_count . ' item(s).</p>';
            echo '<a href="userhome.php" class="back-link">Order More Food!</a>';
        } else {
            echo '<p class="error-message">Oops! Order Failed.</p>';
            if ($ordered_items_count == 0) {
                echo '<p style="font-size:1.1em; color:#777;">No items were selected or a valid Customer ID was not provided.</p>';
            } else {
                echo '<p style="font-size:1.1em; color:#777;">Some items might not have been ordered due to an error. Please try again.</p>';
            }
            echo '<a href="userhome.php" class="back-link">Try Ordering Again</a>';
        }

        $conn->close();
        ?>
    </div>
</body>
</html>