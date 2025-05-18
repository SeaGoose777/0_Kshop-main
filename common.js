// Notification system
function showNotification(message, type = 'info') {
    // Create notification container if it doesn't exist
    let notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'notification-container';
        document.body.appendChild(notificationContainer);
    }

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;

    // Add to container
    notificationContainer.appendChild(notification);

    // Trigger animation
    setTimeout(() => {
        notification.classList.add('visible');
    }, 10);

    // Remove after delay
    setTimeout(() => {
        notification.classList.remove('visible');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Cart functionality
let cart = [];
let totalPrice = 0;

async function loadCart() {
    try {
        const response = await fetch('cart_api.php?action=get');
        const data = await response.json();
        if (data.success) {
            cart = data.cart;
            updateCartDisplay();
        }
    } catch (error) {
        console.error('Error loading cart:', error);
    }
}

async function addToCart(product, quantity) {
    try {
        const formData = new FormData();
        formData.append('product_id', product.id);
        formData.append('quantity', quantity);

        const response = await fetch('cart_api.php?action=add', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            await loadCart();
            showNotification(`${product.name} (${quantity}x) added to cart`, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Error adding to cart', 'error');
    }
}

async function removeFromCart(productId) {
    try {
        const formData = new FormData();
        formData.append('product_id', productId);

        const response = await fetch('cart_api.php?action=remove', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            await loadCart();
            showNotification('Item removed from cart', 'info');
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
        showNotification('Error removing from cart', 'error');
    }
}

async function clearCart() {
    try {
        const response = await fetch('cart_api.php?action=clear');
        const data = await response.json();
        
        if (data.success) {
            await loadCart();
            showNotification('Cart cleared successfully', 'info');
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error('Error clearing cart:', error);
        showNotification('Error clearing cart', 'error');
    }
}

function updateCartDisplay() {
    const cartModalItems = document.getElementById('cart-modal-items');
    const cartModalTotal = document.getElementById('cart-modal-total');
    const cartCount = document.querySelector('.cart-count');

    if (cartCount) {
        let totalItems = 0;
        cart.forEach(item => {
            totalItems += item.quantity;
        });
        cartCount.textContent = totalItems;
    }

    if (cartModalItems && cartModalTotal) {
        cartModalItems.innerHTML = '';
        totalPrice = 0;

        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            totalPrice += itemTotal;

            const modalLi = document.createElement('li');
            modalLi.className = 'cart-modal-item';
            modalLi.innerHTML = `
                <div class="cart-item-image">
                    <img src="${item.image_path}" alt="${item.name}">
                </div>
                <div class="cart-item-details">
                    <h4>${item.name}</h4>
                    <p>Quantity: ${item.quantity}</p>
                    <p>Price: ₱${itemTotal.toLocaleString()}</p>
                </div>
                <button class="remove-item" onclick="removeFromCart(${item.product_id})">×</button>
            `;
            cartModalItems.appendChild(modalLi);
        });
        
        cartModalTotal.textContent = `₱${totalPrice.toLocaleString()}`;
    }
}

// Initialize cart functionality
document.addEventListener('DOMContentLoaded', () => {
    // Load cart if user is logged in
    if (document.querySelector('.cart-icon')) {
        loadCart();
    }

    // Initialize cart modal if it exists
    const cartIcon = document.querySelector('.cart-icon');
    const cartModal = document.getElementById('cart-modal');
    const closeModal = document.querySelector('.close-modal');

    if (cartIcon && cartModal && closeModal) {
        cartIcon.addEventListener('click', () => {
            if (!document.querySelector('.user-welcome')) {
                showNotification('Please log in to view your cart', 'warning');
                return;
            }
            cartModal.style.display = 'block';
        });

        closeModal.addEventListener('click', () => {
            cartModal.style.display = 'none';
        });

        window.addEventListener('click', (event) => {
            if (event.target === cartModal) {
                cartModal.style.display = 'none';
            }
        });
    }

    // Initialize checkout and remove all buttons if they exist
    const checkoutBtn = document.getElementById('modal-checkout');
    const removeAllBtn = document.getElementById('modal-remove');

    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            if (cart.length > 0) {
                showNotification('Thank you for your purchase!', 'success');
                clearCart();
            } else {
                showNotification('Your shopping cart is empty!', 'warning');
            }
        });
    }

    if (removeAllBtn) {
        removeAllBtn.addEventListener('click', () => {
            if (cart.length > 0) {
                clearCart();
            } else {
                showNotification('Your cart is already empty', 'info');
            }
        });
    }
}); 