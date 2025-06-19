<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("location:login.php");
    exit(); // Always exit after a header redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GEC Palakkad Canteen - Order</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="styles/userhome.css" rel="stylesheet">

    
</head>
<body>
    <header>
        <img src="images/logo.png" alt="logo" style="width:150px">
        <div class="header-controls">
            <a id="cartButton">
                <i class="fas fa-shopping-cart"></i> Cart (<span id="cartItemCount">0</span>)
            </a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </header>

    <section class="hero-section">
        <h2>Taste the Tradition, Feel the Flavor!</h2>
        <p>Savor authentic and delicious meals from your campus canteen, delivered right to you.</p>
    </section>

    <div class="container">
        <h2 class="section-title">Our Delicious Menu</h2>
        
        <div class="food-menu-grid">
            <?php
            $host = 'localhost';
            $username = 'root';
            $password = '';
            $dbname = 'user';

            $conn = new mysqli($host, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT * FROM menu";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="food-item-card">
                       
                        <div class="card-content">
                            <div>
                                <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                                <p class="item-category">Category: <?php echo htmlspecialchars($row['category']); ?></p>
                            </div>
                            <p class="item-price">₹<span class="price-value"><?php echo number_format($row['item_price'], 2); ?></span></p>
                            
                            <div class="quantity-selector">
                                <button class="quantity-minus-btn" data-item-id="<?php echo htmlspecialchars($row['item_id']); ?>">-</button>
                                <span class="quantity-display" id="qty_display_<?php echo htmlspecialchars($row['item_id']); ?>">1</span>
                                <button class="quantity-plus-btn" data-item-id="<?php echo htmlspecialchars($row['item_id']); ?>">+</button>
                            </div>

                            <button class="add-to-cart-btn" 
                                    data-item-id="<?php echo htmlspecialchars($row['item_id']); ?>" 
                                    data-item-name="<?php echo htmlspecialchars($row['item_name']); ?>"
                                    data-item-price="<?php echo htmlspecialchars($row['item_price']); ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='text-align: center; grid-column: 1 / -1; font-size: 1.2em; color: var(--text-dark);'>No delicious food items available yet. Please check back later!</p>";
            }
            ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> GEC Palakkad Canteen Management. All rights reserved.</p>
    </footer>

    <div id="cartModal">
        <button id="cartModalClose">&times;</button>
        <h3>Your Cart</h3>
        <form action="order.php" method="post" id="finalOrderForm">
            <ul id="cartItemsList">
                <li id="emptyCartMessage" style="text-align: center; color: #777; font-style: italic;">Your cart is empty.</li>
            </ul>

            <p id="cartTotal">Total: ₹0.00</p>

            <div class="final-order-form">
                <label for="customer_id">Your Customer ID:</label>
                <input type="text" id="customer_id" name="customer_id" required>
            </div>

            <button type="submit" class="place-order-button" disabled>Place Order Now</button>
        </form>
    </div>

    <script>
        // --- Client-side Cart Management Logic ---
        let cart = {}; // Stores {item_id: {name, price, quantity}}
        
        // References to DOM elements
        const cartButton = document.getElementById('cartButton');
        const cartItemCountSpan = document.getElementById('cartItemCount');
        const cartModal = document.getElementById('cartModal');
        const cartModalCloseButton = document.getElementById('cartModalClose');
        const cartItemsList = document.getElementById('cartItemsList');
        const emptyCartMessage = document.getElementById('emptyCartMessage');
        const cartTotalDisplay = document.getElementById('cartTotal');
        const placeOrderButton = document.querySelector('#cartModal .place-order-button');
        const finalOrderForm = document.getElementById('finalOrderForm');

        // Function to update the cart display (modal and header count)
        function updateCartDisplay() {
            cartItemsList.innerHTML = ''; // Clear current cart items
            let totalItemsInCart = 0;
            let totalCartAmount = 0;
            let hasItems = false;

            // Sort cart items by name for consistent display
            const sortedItemIds = Object.keys(cart).sort((a, b) => {
                const nameA = cart[a].name.toLowerCase();
                const nameB = cart[b].name.toLowerCase();
                return nameA.localeCompare(nameB);
            });


            for (const itemId of sortedItemIds) {
                if (cart.hasOwnProperty(itemId) && cart[itemId].quantity > 0) {
                    hasItems = true;
                    const item = cart[itemId];
                    totalItemsInCart += item.quantity;
                    totalCartAmount += item.price * item.quantity;

                    const listItem = document.createElement('li');
                    listItem.classList.add('cart-item');
                    listItem.innerHTML = `
                        <div class="cart-item-info">
                            <span>${item.name}</span>
                            <span>₹${item.price.toFixed(2)} each</span>
                        </div>
                        <div class="cart-item-controls">
                            <button type="button" class="cart-quantity-minus" data-item-id="${itemId}">-</button>
                            <span class="cart-item-quantity" data-item-id="${itemId}">${item.quantity}</span>
                            <button type="button" class="cart-quantity-plus" data-item-id="${itemId}">+</button>
                            <button type="button" class="remove-item-btn" data-item-id="${itemId}">Remove</button>
                        </div>
                    `;
                    cartItemsList.appendChild(listItem);
                }
            }

            // Show/hide empty message
            if (!hasItems) {
                // Only append if it's not already there
                if (!cartItemsList.contains(emptyCartMessage)) {
                    cartItemsList.appendChild(emptyCartMessage);
                }
                placeOrderButton.disabled = true; // Disable order button if cart is empty
            } else {
                // Ensure message is removed if items are present
                if (cartItemsList.contains(emptyCartMessage)) {
                    emptyCartMessage.remove();
                }
                placeOrderButton.disabled = false; // Enable order button
            }

            cartItemCountSpan.textContent = totalItemsInCart;
            cartTotalDisplay.textContent = `Total: ₹${totalCartAmount.toFixed(2)}`;

            // Re-attach event listeners for dynamically created buttons in the cart modal
            attachCartModalItemListeners();
            updateHiddenFormInputs(); // Update form inputs for submission
        }

        // Function to attach listeners for quantity +/- and remove buttons in the cart modal
        function attachCartModalItemListeners() {
            document.querySelectorAll('.cart-quantity-minus').forEach(button => {
                button.onclick = function() {
                    const itemId = this.dataset.itemId;
                    if (cart[itemId] && cart[itemId].quantity > 1) {
                        cart[itemId].quantity--;
                    } else {
                        // If quantity is 1 and minus is pressed, remove item from cart
                        delete cart[itemId];
                    }
                    updateCartDisplay();
                };
            });

            document.querySelectorAll('.cart-quantity-plus').forEach(button => {
                button.onclick = function() {
                    const itemId = this.dataset.itemId;
                    if (cart[itemId]) { // Should always be true if button exists
                        cart[itemId].quantity++;
                    }
                    updateCartDisplay();
                };
            });

            document.querySelectorAll('.remove-item-btn').forEach(button => {
                button.onclick = function() {
                    const itemId = this.dataset.itemId;
                    delete cart[itemId];
                    updateCartDisplay();
                };
            });
        }

        // Function to create/update hidden inputs for form submission
        function updateHiddenFormInputs() {
            // Remove existing hidden inputs for items
            // This is crucial to prevent old items from being submitted
            document.querySelectorAll('#finalOrderForm input[name^="items["]').forEach(input => input.remove());

            // Add new hidden inputs based on current cart state
            for (const itemId in cart) {
                if (cart.hasOwnProperty(itemId) && cart[itemId].quantity > 0) {
                    const item = cart[itemId];
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `items[${itemId}]`; // This structure is crucial for order.php
                    input.value = item.quantity;
                    finalOrderForm.appendChild(input);
                }
            }
        }

        // --- Event Listeners for main page ---
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize quantity displays on page load
            document.querySelectorAll('.quantity-display').forEach(span => {
                span.textContent = '1'; // Default quantity for display
            });

            // Quantity +/- buttons for individual menu items
            document.querySelectorAll('.quantity-minus-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const quantityDisplay = document.getElementById(`qty_display_${itemId}`);
                    let currentQty = parseInt(quantityDisplay.textContent);
                    if (currentQty > 1) {
                        currentQty--;
                        quantityDisplay.textContent = currentQty;
                    }
                });
            });

            document.querySelectorAll('.quantity-plus-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const quantityDisplay = document.getElementById(`qty_display_${itemId}`);
                    let currentQty = parseInt(quantityDisplay.textContent);
                    currentQty++;
                    quantityDisplay.textContent = currentQty;
                });
            });

            // Add to Cart buttons on menu items
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const itemName = this.dataset.itemName;
                    const itemPrice = parseFloat(this.dataset.itemPrice);
                    const quantityDisplay = document.getElementById(`qty_display_${itemId}`);
                    const selectedQuantity = parseInt(quantityDisplay.textContent);

                    if (selectedQuantity < 1) {
                        alert('Please select a quantity of at least 1.');
                        return;
                    }

                    if (cart[itemId]) {
                        cart[itemId].quantity += selectedQuantity; // Add to existing quantity
                    } else {
                        cart[itemId] = {
                            name: itemName,
                            price: itemPrice,
                            quantity: selectedQuantity
                        };
                    }
                    updateCartDisplay();
                    cartModal.classList.add('open'); // Open cart modal when an item is added
                    quantityDisplay.textContent = '1'; // Reset quantity display on menu item
                });
            });

            // Open/Close Cart Modal
            cartButton.addEventListener('click', () => {
                cartModal.classList.toggle('open');
            });

            cartModalCloseButton.addEventListener('click', () => {
                cartModal.classList.remove('open');
            });

            // Initial cart display
            updateCartDisplay(); 
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
