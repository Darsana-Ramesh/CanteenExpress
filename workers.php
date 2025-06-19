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

// --- Process Delete Worker Request (or Add Worker if you were to add it here) ---
// This block will execute when the delete form (or add form) is submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the delete worker form was submitted
    if (isset($_POST['delete_worker_submit'])) {
        $worker_id = trim($_POST['worker_id']); // Trim whitespace

        // Basic validation
        if (empty($worker_id)) {
            $_SESSION['message'] = '<p style="text-align: center;">Error: Worker ID is required to delete a worker.</p>';
            $_SESSION['message_type'] = 'error';
        } else {
            // IMPORTANT: Use prepared statements to prevent SQL Injection
            $sql = "DELETE FROM worker WHERE worker_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $worker_id); // 's' denotes string type for worker_id

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    // Set success message in session
                    $_SESSION['message'] = '<p style="text-align: center;font-size:25px; color: #171747;">One Worker has been Deleted</p>';
                    $_SESSION['message_type'] = 'success';
                } else {
                    // Set error message if no rows were affected (worker_id not found)
                    $_SESSION['message'] = '<p style="text-align: center;">Error: Worker with ID "' . htmlspecialchars($worker_id) . '" not found.</p>';
                    $_SESSION['message_type'] = 'error';
                }
            } else {
                // Set error message for database error
                $_SESSION['message'] = "<p style='text-align: center;'>Error deleting worker: " . $stmt->error . "</p>";
                $_SESSION['message_type'] = 'error';
            }
            $stmt->close();
        }

        // Redirect to the same page after processing the POST request
        // This prevents form resubmission on page refresh (PRG pattern)
        header("Location: workers.php");
        exit(); // Always exit after a header redirect
    }

    // Check if the add new worker form was submitted
    if (isset($_POST['new_worker_submit'])) {
        $worker_id = trim($_POST['new_worker_id']);
        $worker_name = trim($_POST['new_worker_name']);
        $worker_section = trim($_POST['new_worker_section']);

        // Basic validation
        if (empty($worker_id) || empty($worker_name) || empty($worker_section)) {
            $_SESSION['message'] = '<p style="text-align: center;">Error: All fields are required to add a new worker.</p>';
            $_SESSION['message_type'] = 'error';
        } else {
            $sql_add = "INSERT INTO worker (worker_id, worker_name, worker_section) VALUES (?, ?, ?)";
            $stmt_add = $conn->prepare($sql_add);
            $stmt_add->bind_param("sss", $worker_id, $worker_name, $worker_section);

            if ($stmt_add->execute()) {
                $_SESSION['message'] = '<p style="text-align: center;font-size:25px; color: #171747;">New Worker added Successfully</p>';
                $_SESSION['message_type'] = 'success';
            } else {
                if ($conn->errno == 1062) { // Duplicate entry error code for MySQL
                    $_SESSION['message'] = "<p style='text-align: center;'>Error: Worker ID '" . htmlspecialchars($worker_id) . "' already exists. Please choose a unique ID.</p>";
                } else {
                    $_SESSION['message'] = "<p style='text-align: center;'>Error adding worker: " . $stmt_add->error . "</p>";
                }
                $_SESSION['message_type'] = 'error';
            }
            $stmt_add->close();
        }
        header("Location: workers.php");
        exit();
    }
}

// --- Fetch Worker Data for Display ---
// This happens after any POST operations, so the table shows the latest data.
$sql = "SELECT worker_id, worker_name, worker_section FROM worker ORDER BY worker_id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Workers - CanteenExpress Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles/workers.css">
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

    <h1 class="page-title">Worker Management</h1>

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
                    <th>WORKER ID</th>
                    <th>WORKER NAME</th>
                    <th>WORKING SECTION</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['worker_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['worker_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['worker_section']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align: center; padding: 20px;'>No workers found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="form-section">
        <form action="workers.php" method="post" class="action-form">
            <h2>Add New Worker</h2>
            <input type="hidden" name="new_worker_submit" value="1">

            <label for="new_worker_id">Worker ID:</label>
            <input type="text" id="new_worker_id" name="new_worker_id" placeholder="e.g., WKR001" required>

            <label for="new_worker_name">Worker Name:</label>
            <input type="text" id="new_worker_name" name="new_worker_name" placeholder="e.g., John Doe" required>

            <label for="new_worker_section">Working Section:</label>
            <input type="text" id="new_worker_section" name="new_worker_section" placeholder="e.g., Kitchen" required>

            <button type="submit" class="submit-btn"><i class="fas fa-user-plus"></i> Add Worker</button>
        </form>

        <form action="workers.php" method="post" class="action-form">
            <h2>Delete Worker</h2>
            <input type="hidden" name="delete_worker_submit" value="1">

            <label for="delete_worker_id">Worker ID to Delete:</label>
            <input type="text" id="delete_worker_id" name="worker_id" placeholder="e.g., WKR001" required>

            <button type="submit" class="submit-btn delete-btn"><i class="fas fa-user-minus"></i> Delete Worker</button>
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