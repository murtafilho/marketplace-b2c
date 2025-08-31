import './bootstrap';
import Alpine from 'alpinejs';

// Global utilities FIRST
window.Alpine = Alpine;
window.formatCurrency = (value) => {
    return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    });
};
window.formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

// Simple stores without external dependencies
Alpine.store('cart', {
    items: [],
    open: false,
    loading: false,
    
    get count() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    },
    
    get subtotal() {
        return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    },
    
    addItem(product, quantity = 1) {
        if (!product || !product.id) return;
        
        const existingItem = this.items.find(item => item.id === product.id);
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.items.push({
                id: product.id,
                name: product.name,
                price: product.price,
                quantity: quantity,
                added_at: new Date().toISOString()
            });
        }
        
        // Save to localStorage
        try {
            localStorage.setItem('cart_items', JSON.stringify(this.items));
        } catch (e) {
            console.warn('Could not save cart to localStorage');
        }
    },
    
    removeItem(productId) {
        const index = this.items.findIndex(item => item.id === productId);
        if (index > -1) {
            this.items.splice(index, 1);
            try {
                localStorage.setItem('cart_items', JSON.stringify(this.items));
            } catch (e) {
                console.warn('Could not save cart to localStorage');
            }
        }
    },
    
    toggleCart() {
        this.open = !this.open;
    },
    
    init() {
        // Load from localStorage
        try {
            const saved = localStorage.getItem('cart_items');
            if (saved) {
                this.items = JSON.parse(saved);
            }
        } catch (e) {
            console.warn('Could not load cart from localStorage');
        }
    }
});

Alpine.store('ui', {
    mobileMenu: false,
    searchOpen: false,
    globalLoading: false,
    scrolled: false,
    currentTheme: 'light',
    
    init() {
        // Initialize theme from localStorage
        const savedTheme = localStorage.getItem('theme') || 'light';
        this.currentTheme = savedTheme;
        document.documentElement.classList.toggle('dark', savedTheme === 'dark');
    },
    
    openMobileMenu() {
        this.mobileMenu = true;
    },
    
    closeMobileMenu() {
        this.mobileMenu = false;
    },
    
    toggleMobileMenu() {
        this.mobileMenu = !this.mobileMenu;
    },
    
    openSearch() {
        this.searchOpen = true;
    },
    
    closeSearch() {
        this.searchOpen = false;
    },
    
    toggleSearch() {
        this.searchOpen = !this.searchOpen;
    },
    
    toggleTheme() {
        this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        document.documentElement.classList.toggle('dark', this.currentTheme === 'dark');
        localStorage.setItem('theme', this.currentTheme);
    },
    
    handleScroll() {
        this.scrolled = window.scrollY > 50;
    }
});

Alpine.store('notifications', {
    items: [],
    
    add(message, type = 'info') {
        const notification = {
            id: Date.now(),
            message,
            type,
            createdAt: new Date()
        };
        
        this.items.unshift(notification);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            this.remove(notification.id);
        }, 4000);
        
        return notification.id;
    },
    
    remove(id) {
        const index = this.items.findIndex(item => item.id === id);
        if (index > -1) {
            this.items.splice(index, 1);
        }
    },
    
    success(message) {
        return this.add(message, 'success');
    },
    
    error(message) {
        return this.add(message, 'error');
    }
});

// Start Alpine
console.log('Starting Alpine...');
Alpine.start();

// Layout functionality removed - using simplified approach

// Initialize stores after Alpine is ready
document.addEventListener('alpine:init', () => {
    console.log('Alpine ready, initializing stores...');
    if (Alpine.store('cart')) {
        Alpine.store('cart').init();
    }
    if (Alpine.store('ui')) {
        Alpine.store('ui').init();
    }
});