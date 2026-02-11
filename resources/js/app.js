// Alpine.js for interactivity (loaded via CDN in layout)
import './bootstrap';

// Cart functionality
window.Cart = {
    items: JSON.parse(localStorage.getItem('cart') || '[]'),

    add(menuId, name, price, image = null) {
        const existingIndex = this.items.findIndex(item => item.id === menuId);

        if (existingIndex > -1) {
            this.items[existingIndex].quantity++;
        } else {
            this.items.push({
                id: menuId,
                name: name,
                price: price,
                image: image,
                quantity: 1
            });
        }

        this.save();
        this.updateBadge();
        this.showToast('Ditambahkan ke keranjang!');
    },

    remove(menuId) {
        this.items = this.items.filter(item => item.id !== menuId);
        this.save();
        this.updateBadge();
    },

    updateQuantity(menuId, quantity) {
        const item = this.items.find(item => item.id === menuId);
        if (item) {
            item.quantity = Math.max(0, quantity);
            if (item.quantity === 0) {
                this.remove(menuId);
            } else {
                this.save();
            }
        }
        this.updateBadge();
    },

    getTotal() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    },

    getCount() {
        return this.items.reduce((count, item) => count + item.quantity, 0);
    },

    clear() {
        this.items = [];
        this.save();
        this.updateBadge();
    },

    save() {
        localStorage.setItem('cart', JSON.stringify(this.items));
        window.dispatchEvent(new CustomEvent('cart-updated'));
    },

    removeByIndex(index) {
        if (index >= 0 && index < this.items.length) {
            this.items.splice(index, 1);
            this.save();
            this.updateBadge();
        }
    },

    updateQuantityByIndex(index, quantity) {
        if (index >= 0 && index < this.items.length) {
            const item = this.items[index];
            item.quantity = Math.max(0, quantity);
            if (item.quantity === 0) {
                this.removeByIndex(index);
            } else {
                this.save();
                this.updateBadge();
            }
        }
    },

    updateBadge() {
        const badge = document.getElementById('cart-badge');
        const count = this.getCount();
        if (badge) {
            badge.textContent = count;
            badge.classList.toggle('hidden', count === 0);
        }
    },

    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    },

    formatPrice(price) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
    }
};

// Initialize cart badge on page load
document.addEventListener('DOMContentLoaded', () => {
    Cart.updateBadge();
});

// Mobile menu toggle
window.toggleMobileMenu = () => {
    const menu = document.getElementById('mobile-menu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
};
