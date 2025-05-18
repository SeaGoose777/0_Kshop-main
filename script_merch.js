// Cart functionality
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// Define addToCart function first
function addToCart(product, quantity) {
    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            quantity: quantity,
            image: product.image
        });
    }
    
    // Save to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update cart count
    updateCartCount();
    
    // Show success notification
    showNotification('Item added to cart', 'success');
}

function updateCartCount() {
    const cartCount = document.querySelector('.cart-count');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
}

function updateCartModal() {
    const cartItems = document.getElementById('cart-modal-items');
    const cartTotal = document.getElementById('cart-modal-total');
    
    // Clear current items
    cartItems.innerHTML = '';
    
    // Calculate total
    let total = 0;
    
    // Add each item to the modal
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        const li = document.createElement('li');
        li.className = 'cart-modal-item';
        li.innerHTML = `
            <div class="cart-item-image">
                <img src="${item.image}" alt="${item.name}">
            </div>
            <div class="cart-item-details">
                <h4>${item.name}</h4>
                <p class="item-price">₱${item.price.toFixed(2)}</p>
                <div class="cart-quantity-control">
                    <button class="quantity-btn minus" data-id="${item.id}">-</button>
                    <input type="number" class="quantity-input" value="${item.quantity}" min="1" max="99" readonly>
                    <button class="quantity-btn plus" data-id="${item.id}">+</button>
                </div>
            </div>
            <div class="cart-item-total">
                <p>₱${(itemTotal).toFixed(2)}</p>
                <button class="remove-item" data-id="${item.id}">×</button>
            </div>
        `;
        cartItems.appendChild(li);
    });
    
    // Update total
    cartTotal.textContent = `₱${total.toFixed(2)}`;

    // Add event listeners for quantity buttons in cart
    document.querySelectorAll('.cart-quantity-control .quantity-btn').forEach(button => {
        button.addEventListener('click', () => {
            const itemId = button.dataset.id;
            const input = button.parentElement.querySelector('.quantity-input');
            const currentValue = parseInt(input.value);
            
            if (button.classList.contains('plus')) {
                input.value = currentValue + 1;
            } else if (button.classList.contains('minus') && currentValue > 1) {
                input.value = currentValue - 1;
            }
            
            // Update cart item quantity
            const cartItem = cart.find(item => item.id === itemId);
            if (cartItem) {
                cartItem.quantity = parseInt(input.value);
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartCount();
                updateCartModal();
            }
        });
    });
}

// Add this function after updateCartModal function
function processCheckout() {
    // Get cart total
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    // Create confirmation message
    const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    const confirmMessage = `Are you sure you want to checkout?\n\nTotal Items: ${itemCount}\nTotal Amount: ₱${total.toFixed(2)}`;
    
    if (confirm(confirmMessage)) {
        // Here you would typically send the order to your server
        // For now, we'll just simulate a successful checkout
        
        // Clear the cart
        cart = [];
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Update UI
        updateCartCount();
        updateCartModal();
        
        // Close the modal
        document.getElementById('cart-modal').style.display = 'none';
        
        // Show success message
        showNotification('Thank you for your purchase! Your order has been placed.', 'success');
    }
}

// Wait for DOM to be fully loaded
window.addEventListener('load', function() {
    // Initialize cart count
    updateCartCount();

    // Cart modal functionality
    const cartIcon = document.querySelector('.cart-icon');
    const cartModal = document.getElementById('cart-modal');
    const closeModal = document.querySelector('.close-modal');
    const removeAllBtn = document.getElementById('modal-remove');
    const checkoutBtn = document.getElementById('modal-checkout');

    // Open modal when clicking cart icon
    cartIcon.addEventListener('click', () => {
        if (!document.querySelector('.user-welcome')) {
            showNotification('Please log in to view your cart', 'warning');
            return;
        }
        updateCartModal();
        cartModal.style.display = 'block';
    });

    // Close modal when clicking the X
    closeModal.addEventListener('click', () => {
        cartModal.style.display = 'none';
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === cartModal) {
            cartModal.style.display = 'none';
        }
    });

    // Remove all items
    removeAllBtn.addEventListener('click', () => {
        cart = [];
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        updateCartModal();
        showNotification('Cart cleared', 'info');
    });

    // Remove individual items
    document.getElementById('cart-modal-items').addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-item')) {
            const itemId = e.target.dataset.id;
            cart = cart.filter(item => item.id !== itemId);
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
            updateCartModal();
            showNotification('Item removed from cart', 'info');
        }
    });

    // Update checkout button handler
    checkoutBtn.addEventListener('click', () => {
        if (cart.length === 0) {
            showNotification('Your cart is empty', 'warning');
            return;
        }
        processCheckout();
    });

    // Add quantity button handlers
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', () => {
            const input = button.parentElement.querySelector('.quantity-input');
            const currentValue = parseInt(input.value);
            const maxValue = parseInt(input.max);
            
            if (button.classList.contains('plus') && currentValue < maxValue) {
                input.value = currentValue + 1;
            } else if (button.classList.contains('minus') && currentValue > 1) {
                input.value = currentValue - 1;
            }
            
            // Update button states
            const minusBtn = button.parentElement.querySelector('.minus');
            const plusBtn = button.parentElement.querySelector('.plus');
            minusBtn.disabled = input.value <= 1;
            plusBtn.disabled = input.value >= maxValue;
        });
    });

    // Initialize quantity button states
    document.querySelectorAll('.quantity-control').forEach(control => {
        const input = control.querySelector('.quantity-input');
        const minusBtn = control.querySelector('.minus');
        const plusBtn = control.querySelector('.plus');
        
        minusBtn.disabled = input.value <= 1;
        plusBtn.disabled = input.value >= parseInt(input.max);
    });

    // Add to cart button handlers
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', () => {
            if (!document.querySelector('.user-welcome')) {
                showNotification('Please log in to add items to your cart', 'warning');
                return;
            }

            const productElement = button.closest('.product');
            const product = {
                id: productElement.dataset.id,
                name: productElement.dataset.name,
                price: parseFloat(productElement.dataset.price),
                image: productElement.querySelector('img').src
            };
            const quantity = parseInt(productElement.querySelector('.quantity-input').value);
            addToCart(product, quantity);
        });
    });
}); 