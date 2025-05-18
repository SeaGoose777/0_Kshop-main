// Product-specific functionality
document.addEventListener('DOMContentLoaded', () => {
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
                price: parseFloat(productElement.dataset.price)
            };
            const quantity = parseInt(productElement.querySelector('.quantity-input').value);
            addToCart(product, quantity);
        });
    });
}); 