// Alpine.js for interactivity (loaded via CDN in layout)
import './bootstrap';

// Unified Cart functionality
window.Cart = {
    items: JSON.parse(localStorage.getItem('cart') || '[]'),
    tableNumber: window.appTableNumber || null, // Will be injected by blade

    // Get API Base URL based on context (QR or General)
    get baseUrl() {
        return this.tableNumber
            ? `/order/${this.tableNumber}/cart`
            : `/cart-api`; // Direct API for general cart
    },

    get headers() {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token || ''
        };
    },

    async init() {
        // Sync with server on load
        await this.syncFromServer();
    },

    async syncFromServer() {
        try {
            const endpoint = this.baseUrl; // Use getter
            const response = await fetch(endpoint, {
                method: 'GET',
                headers: this.headers
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.updateLocalState(data.cart);
                }
            }
        } catch (error) {
            console.error('Failed to sync cart:', error);
        }
    },

    async add(menuId, name, price, image = null, quantity = 1, options = {}) {
        try {
            const endpoint = this.baseUrl; // Use getter
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: this.headers,
                body: JSON.stringify({
                    menu_id: menuId,
                    quantity: quantity,
                    options: options,
                    table_number: this.tableNumber
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.updateLocalState(data.cart);
                    this.showToast('Ditambahkan ke keranjang!');
                } else {
                    this.showToast(data.message || 'Gagal menambahkan item', 'error');
                }
            } else {
                this.showToast('Terjadi kesalahan server', 'error');
            }
        } catch (error) {
            console.error('Cart add error:', error);
            this.showToast('Gagal terhubung ke server', 'error');
        }
    },

    async remove(cartItemId) {
        try {
            const endpoint = this.baseUrl + '/item'; // Use getter
            const response = await fetch(endpoint, {
                method: 'DELETE',
                headers: this.headers,
                body: JSON.stringify({
                    cartItemId: cartItemId,
                    table_number: this.tableNumber
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.updateLocalState(data.cart);
                }
            }
        } catch (error) {
            console.error('Cart remove error:', error);
        }
    },

    async updateQuantity(cartItemId, quantity) {
        try {
            const endpoint = this.baseUrl + '/item'; // Use getter
            const response = await fetch(endpoint, {
                method: 'PATCH',
                headers: this.headers,
                body: JSON.stringify({
                    cartItemId: cartItemId,
                    quantity: quantity,
                    table_number: this.tableNumber
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.updateLocalState(data.cart);
                }
            }
        } catch (error) {
            console.error('Cart update error:', error);
        }
    },

    // --- Local Helpers ---

    updateLocalState(cart) {
        this.items = cart;
        localStorage.setItem('cart', JSON.stringify(this.items));
        this.updateBadge();
        // Dispatch event for other components to listen
        window.dispatchEvent(new CustomEvent('cart-updated', { detail: cart }));
    },

    updateBadge() {
        const badge = document.getElementById('cart-badge');
        const count = this.items.length;

        if (badge) {
            badge.textContent = count > 99 ? '99+' : count;
            if (count > 0) {
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    },

    // Kept for backward compatibility
    removeByIndex(index) {
        const item = this.items[index];
        if (item && item.cartItemId) {
            this.remove(item.cartItemId);
        }
    },

    updateQuantityByIndex(index, quantity) {
        const item = this.items[index];
        if (item && item.cartItemId) {
            this.updateQuantity(item.cartItemId, quantity);
        }
    },

    clear() {
        this.items = [];
        localStorage.removeItem('cart');
        this.updateBadge();
    },

    formatPrice(price) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
    },

    showToast(message, type = 'success') {
        const colors = {
            success: 'bg-[#d47311]', // Primary color
            error: 'bg-red-500',
            info: 'bg-blue-500'
        };

        const toast = document.createElement('div');
        // Tailwind classes for positioning and style
        toast.className = `fixed bottom-4 left-1/2 -translate-x-1/2 z-50 px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 text-white transform transition-all duration-300 translate-y-10 opacity-0 ${colors[type] || colors.success}`;
        toast.innerHTML = `
            <span class="material-symbols-outlined text-[20px]">${type === 'error' ? 'error' : 'check_circle'}</span>
            <span class="font-medium">${message}</span>
        `;
        document.body.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
        });

        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
};

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', () => {
    // Check for global table number override injected by blade
    if (window.appTableNumber) {
        window.Cart.tableNumber = window.appTableNumber;
    }

    window.Cart.updateBadge(); // Initial render
    window.Cart.init(); // Sync with server
});

// Mobile menu toggle
window.toggleMobileMenu = () => {
    const menu = document.getElementById('mobile-menu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
};
