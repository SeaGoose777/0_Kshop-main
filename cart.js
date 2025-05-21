class ShoppingCart {
    constructor() {
        this.cart = [];
        this.total = 0;
        this.cartIcon = document.getElementById('cart-icon');
        this.cartSidebar = document.getElementById('cart-sidebar');
        this.cartItems = document.getElementById('cart-items');
        this.cartTotal = document.getElementById('cart-total');
        this.closeCart = document.getElementById('close-cart');
        this.cartCount = document.querySelector('.cart-count');
        this.checkoutBtn = document.querySelector('.checkout-btn');

        this.init();
        this.loadCartFromStorage();
    }

    init() {
        // Event Listeners
        if (this.cartIcon) {
            this.cartIcon.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleCart();
            });
        }

        if (this.closeCart) {
            this.closeCart.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleCart();
            });
        }

        if (this.checkoutBtn) {
            this.checkoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.checkout();
            });
        }

        // Close cart when clicking outside
        document.addEventListener('click', (e) => {
            if (this.cartSidebar && 
                this.cartSidebar.classList.contains('active') && 
                !this.cartSidebar.contains(e.target) && 
                !this.cartIcon.contains(e.target)) {
                this.toggleCart();
            }
        });

        // Prevent cart from closing when clicking inside
        if (this.cartSidebar) {
            this.cartSidebar.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        // Add event listeners to all add-to-cart buttons
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.addItem(button);
            });
        });
    }

    toggleCart() {
        if (this.cartSidebar) {
            this.cartSidebar.classList.toggle('active');
            if (this.cartSidebar.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
    }

    addItem(button) {
        const item = {
            id: button.dataset.id,
            name: button.dataset.name,
            price: parseFloat(button.dataset.price),
            quantity: 1
        };

        const existingItem = this.cart.find(cartItem => cartItem.id === item.id);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.cart.push(item);
        }

        this.updateCart();
        this.showNotification(`${item.name} added to cart!`);
    }

    removeItem(id) {
        this.cart = this.cart.filter(item => item.id !== id);
        this.updateCart();
    }

    updateQuantity(id, change) {
        const item = this.cart.find(item => item.id === id);
        if (item) {
            item.quantity = Math.max(1, item.quantity + change);
            this.updateCart();
        }
    }

    updateCart() {
        if (!this.cartItems) return;

        this.cartItems.innerHTML = '';
        this.total = 0;

        this.cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            this.total += itemTotal;

            const cartItemElement = document.createElement('div');
            cartItemElement.className = 'cart-item';
            cartItemElement.innerHTML = `
                <div class="cart-item-info">
                    <h4>${item.name}</h4>
                    <p>$${item.price.toFixed(2)}</p>
                </div>
                <div class="cart-item-quantity">
                    <button class="quantity-btn" onclick="window.shoppingCart.updateQuantity('${item.id}', -1)">-</button>
                    <span>${item.quantity}</span>
                    <button class="quantity-btn" onclick="window.shoppingCart.updateQuantity('${item.id}', 1)">+</button>
                </div>
                <div class="cart-item-total">
                    <p>$${itemTotal.toFixed(2)}</p>
                    <button class="remove-item" onclick="window.shoppingCart.removeItem('${item.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            this.cartItems.appendChild(cartItemElement);
        });

        if (this.cartTotal) {
            this.cartTotal.textContent = this.total.toFixed(2);
        }

        if (this.cartCount) {
            const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
            this.cartCount.textContent = totalItems;
        }

        this.saveCartToStorage();
    }

    checkout() {
        if (this.cart.length === 0) {
            this.showNotification('Your cart is empty!');
            return;
        }
        
        // Here you would typically integrate with a payment processor
        this.showNotification('Thank you for your purchase!');
        this.cart = [];
        this.updateCart();
        this.toggleCart();
    }

    showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    saveCartToStorage() {
        localStorage.setItem('shoppingCart', JSON.stringify(this.cart));
    }

    loadCartFromStorage() {
        const savedCart = localStorage.getItem('shoppingCart');
        if (savedCart) {
            this.cart = JSON.parse(savedCart);
            this.updateCart();
        }
    }
}

// Initialize cart when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.shoppingCart = new ShoppingCart();
}); 