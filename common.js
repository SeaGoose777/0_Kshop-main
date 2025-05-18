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

// Initialize functionality
document.addEventListener('DOMContentLoaded', () => {
    // Any common initialization code can go here
}); 