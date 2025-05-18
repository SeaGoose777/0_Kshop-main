let cart = []
let totalPrice = 0

function showNotification(message) {
    const notification = document.getElementById('notification')
    notification.textContent = message
    notification.classList.add('visible')

    setTimeout(() => {
        notification.classList.remove('visible')
    }, 3000)
}

function addToCart(product, quantity) {
    const existingProduct = cart.find(item => item.id === product.id)

    if(existingProduct) {
        existingProduct.quantity += quantity
    } else {
        cart.push({...product, quantity})
    }
    totalPrice += product.price * quantity
    
    const cartUpdatedEvent = new CustomEvent('cartUpdated', {
        detail: {cart, totalPrice}
    })
    document.dispatchEvent(cartUpdatedEvent)

    showNotification(`${product.name} (${quantity}x) add to cart`)

    const addButton = document.querySelector(`.product[data-id="${product.id}"] .add-to-cart`)
    addButton.textContent = 'Added to cart'
    // addButton.classList.add('added')
    // addButton.disabled = true
}

function removeFromCart(productId) {
    const productIndex = cart.findIndex(item => item.id === productId)

    if(productIndex !== -1) {
        const product = cart[productIndex]
        if(product.quantity > 1) {
            product.quantity -= 1
        } else {
            cart.splice(productIndex, 1)
        }
        totalPrice -= product.price
    
        const cartUpdatedEvent = new CustomEvent('cartUpdated', {
            detail: { cart, totalPrice }
        })
        document.dispatchEvent(cartUpdatedEvent)

        showNotification(`${product.name} removed from the cart!`)
    } 
}

function updateCartDisplay() {
    const cartItemsElement = document.getElementById('cart-items')
    const totalPriceElement = document.getElementById('total-price')

    cartItemsElement.innerHTML = ''

    cart.forEach(item => {
        const li = document.createElement('li')
        li.textContent = `${item.name} (${item.quantity}x) - ₱ ${(item.price * item.quantity).toLocaleString()}`

        const removeButton = document.createElement('button')
        removeButton.textContent = 'Subtract'
        removeButton.addEventListener('click', () => removeFromCart(item.id))
        li.appendChild(removeButton)

        cartItemsElement.appendChild(li)
    })
    
    totalPriceElement.textContent = `₱ ${totalPrice.toLocaleString()}`
}

document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', () => {
        const productElement = button.closest('.product')
        const product = {
            id: productElement.dataset.id,
            name: productElement.dataset.name,
            price: parseInt(productElement.dataset.price)
        }
        const quantity = parseInt(productElement.querySelector('.quantity-input').value)
        addToCart(product, quantity)
    })
})

document.getElementById('checkout').addEventListener('click', () => {
    if(cart.length > 0) {
        alert(`Thank you, Total prices: ₱${totalPrice.toLocaleString()}`)
        cart = []
        totalPrice = 0
        document.dispatchEvent(new CustomEvent('cartUpdated', {detail: {cart, totalPrice} }))
    } else {
        alert('Shopping cart is empty!')
    }
})

function removeAllFromCart() {
    if (cart.length > 0) {
        cart = []
        totalPrice = 0
        const cartUpdatedEvent = new CustomEvent('cartUpdated', {
            detail: { cart, totalPrice }
        })
        document.dispatchEvent(cartUpdatedEvent); 
        showNotification('All products removed from the cart!')
    } else {
        showNotification('The cart is already empty!')
    }
}

document.getElementById('remove').addEventListener('click', () => {
    removeAllFromCart();
});
document.addEventListener('cartUpdated', () => {
    updateCartDisplay()
})