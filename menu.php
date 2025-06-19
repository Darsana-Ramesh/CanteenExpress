<?php
// Start the session at the very beginning of the script
session_start();

// Database connection details
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user';

// Establish database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Process Form Submissions (Add or Delete Food Item) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Process Add New Food Item Request
    if (isset($_POST['add_item_submit'])) {
        $item_id = trim($_POST['new_item_id']);
        $item_name = trim($_POST['new_item_name']);
        $item_price = trim($_POST['new_item_price']);
        $category = trim($_POST['new_category']);

        // Basic validation
        if (empty($item_id) || empty($item_name) || empty($item_price) || empty($category)) {
            $_SESSION['message'] = '<p style="text-align: center;">Error: All fields are required to add a new food item.</p>';
            $_SESSION['message_type'] = 'error';
        } elseif (!is_numeric($item_price) || $item_price < 0) {
            $_SESSION['message'] = '<p style="text-align: center;">Error: Price must be a non-negative number.</p>';
            $_SESSION['message_type'] = 'error';
        } else {
            // Use prepared statement for INSERT
            $sql_add = "INSERT INTO menu (item_id, item_name, item_price, category) VALUES (?, ?, ?, ?)";
            $stmt_add = $conn->prepare($sql_add);

            // 's' for string, 'd' for double/float
            $stmt_add->bind_param("ssds", $item_id, $item_name, $item_price, $category);

            if ($stmt_add->execute()) {
                $_SESSION['message'] = '<p style="text-align: center;font-size:25px; color: #171747;">New Item added Successfully</p>';
                $_SESSION['message_type'] = 'success';
            } else {
                if ($conn->errno == 1062) { // MySQL error code for duplicate entry (e.g., duplicate item_id)
                    $_SESSION['message'] = '<p style="text-align: center;">Error: Food Item ID "' . htmlspecialchars($item_id) . '" already exists. Please choose a unique ID.</p>';
                } else {
                    $_SESSION['message'] = '<p style="text-align: center;">Error adding item: ' . $stmt_add->error . '</p>';
                }
                $_SESSION['message_type'] = 'error';
            }
            $stmt_add->close();
        }
        // Redirect to prevent form resubmission
        header("Location: menu.php");
        exit();
    }

    // Process Delete Food Item Request
    if (isset($_POST['delete_item_submit'])) {
        $item_id = trim($_POST['delete_item_id']);

        // Basic validation
        if (empty($item_id)) {
            $_SESSION['message'] = '<p style="text-align: center;">Error: Food Item ID is required to delete an item.</p>';
            $_SESSION['message_type'] = 'error';
        } else {
            // Use prepared statement for DELETE
            $sql_delete = "DELETE FROM menu WHERE item_id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("s", $item_id);

            if ($stmt_delete->execute()) {
                if ($stmt_delete->affected_rows > 0) {
                    $_SESSION['message'] = '<p style="text-align: center;font-size:25px; color: #171747;">One Item Has been Deleted Successfully</p>';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = '<p style="text-align: center;">Error: Food Item with ID "' . htmlspecialchars($item_id) . '" not found.</p>';
                    $_SESSION['message_type'] = 'error';
                }
            } else {
                $_SESSION['message'] = '<p style="text-align: center;">Error deleting item: ' . $stmt_delete->error . '</p>';
                $_SESSION['message_type'] = 'error';
            }
            $stmt_delete->close();
        }
        // Redirect to prevent form resubmission
        header("Location: menu.php");
        exit();
    }
}

// --- Fetch Food Item Data for Display ---
$sql = "SELECT item_id, item_name, item_price, category FROM menu ORDER BY item_id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Food Items - CanteenExpress Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles/menu.css">
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

    <h1 class="page-title">- Manage Food Items -</h1>

    <?php
    // Only display the message pop-up if there's a message in the session
    if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
        ?>
        <div id="messagePopup" class="message-popup <?php echo isset($_SESSION['message_type']) ? $_SESSION['message_type'] : ''; ?>">
            <span class="close-btn" onclick="document.getElementById('messagePopup').classList.remove('show');">&times;</span>
            <?php
            echo $_SESSION['message'];
            // Clear the message from session after displaying
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
        </div>
    <?php
    }
    ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Food ID</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['item_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                        // Format price to 2 decimal places
                        echo "<td>" . htmlspecialchars(number_format($row['item_price'], 2)) . "</td>";
                        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align: center; padding: 20px;'>No food items found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="form-section">
        <form action="menu.php" method="post" class="action-form">
            <h2>Add New Food Item</h2>
            <input type="hidden" name="add_item_submit" value="1">

            <label for="new_item_id">Food ID:</label>
            <input type="text" id="new_item_id" name="new_item_id" placeholder="e.g., F001" required>

            <label for="new_item_name">Food Name:</label>
            <input type="text" id="new_item_name" name="new_item_name" placeholder="e.g., Burger" required>

            <label for="new_item_price">Price:</label>
            <input type="number" id="new_item_price" name="new_item_price" step="0.01" min="0" placeholder="e.g., 5.99" required>

            <label for="new_category">Category:</label>
            <input type="text" id="new_category" name="new_category" placeholder="e.g., Main Course" required>

            <button type="submit" class="submit-btn"><i class="fas fa-plus-circle"></i> Add Food Item</button>
        </form>

        <form action="menu.php" method="post" class="action-form">
            <h2>Delete Food Item</h2>
            <input type="hidden" name="delete_item_submit" value="1">

            <label for="delete_item_id">Food ID to Delete:</label>
            <input type="text" id="delete_item_id" name="delete_item_id" placeholder="e.g., F001" required>

            <button type="submit" class="submit-btn delete-btn"><i class="fas fa-minus-circle"></i> Delete Food Item</button>
        </form>
    </div>

    <script>
        // JavaScript to show the pop-up if it exists and has content
        document.addEventListener('DOMContentLoaded', function() {
            const messagePopup = document.getElementById('messagePopup');
            // Check if the messagePopup element actually exists on the page
            if (messagePopup) {
                // The PHP code now ensures the div only exists if there's a message,
                // so we just need to add the 'show' class.
                messagePopup.classList.add('show');

                // Optional: Automatically hide the popup after a few seconds
                // setTimeout(function() {
                //     messagePopup.classList.remove('show');
                // }, 5000); // Hide after 5 seconds
            }
        });
    </script>

</body>
</html>
<?php
// Close database connection
$conn->close();
?>