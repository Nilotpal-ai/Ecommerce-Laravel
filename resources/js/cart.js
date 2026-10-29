// Function to update the top-right badge and add a brief pop animation
window.updateCartBadge = function(count) {
    const badge = document.getElementById('cart-badge');
    if (!badge) return;

    badge.textContent = count;

    if (count > 0) {
        badge.classList.remove('hidden');
        // Pulse animation
        badge.classList.add('scale-125');
        setTimeout(() => badge.classList.remove('scale-125'), 200);
    } else {
        badge.classList.add('hidden');
    }
};

// Global listener for custom events
window.addEventListener('cart-updated', function(event) {
    if (event.detail && typeof event.detail.count !== 'undefined') {
        window.updateCartBadge(event.detail.count);
    }
});