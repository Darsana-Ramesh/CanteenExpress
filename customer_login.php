<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$phno = $_POST['phno'];

$customer_id = $_POST['customer_id']; // Changed variable name to avoid conflict with DB username
$password = $_POST['password'];

$sql = "INSERT INTO login(username,password) VALUES ('$customer_id','$password')"; // Use $customer_id
$sql2 = "INSERT INTO allcustomer(name,customer_id,phno) VALUES ('$name','$customer_id','$phno')"; // Use $customer_id

$successMessage = '';
$errorMessage = '';

if ($conn->query($sql) === TRUE) {
    if ($conn->query($sql2) === TRUE){
        $successMessage = 'Login done successfully!';
    } else {
        $errorMessage = "Error inserting into allcustomer: " . $conn->error;
    }
} else {
    $errorMessage = "Error inserting into login: " . $conn->error;
}
$conn->close();

?>
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

    <div id="statusPopup" class="popup-overlay">
        <div class="popup-content">
            <h3 id="popupMessage"></h3>
            <a href="login.php" class="popup-button">GO TO LOGIN PAGE</a>
        </div>
    </div>

    <script>
        // PHP variables will be embedded here
        const successMessage = "<?php echo $successMessage; ?>";
        const errorMessage = "<?php echo $errorMessage; ?>";

        const popupOverlay = document.getElementById('statusPopup');
        const popupMessage = document.getElementById('popupMessage');

        if (successMessage) {
            popupMessage.textContent = successMessage;
            popupMessage.style.color = '#171747'; // Apply success color
            popupOverlay.classList.add('show');
        } else if (errorMessage) {
            popupMessage.textContent = errorMessage;
            popupMessage.style.color = 'red'; // Apply error color (you can define a CSS variable for this if needed)
            popupOverlay.classList.add('show');
        }

        // Optional: Close popup if clicked outside (useful for error messages)
        popupOverlay.addEventListener('click', function(event) {
            if (event.target === popupOverlay && errorMessage) { // Only close on overlay click for errors
                popupOverlay.classList.remove('show');
            }
        });
    </script>
</body>
</html>