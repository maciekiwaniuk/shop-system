import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';
import { CartItem, Cart } from '@/types/cart';
import { Product } from '@/types/product';

interface CartState {
    items: CartItem[];
    addItem: (product: Product, quantity?: number) => void;
    removeItem: (productId: number) => void;
    updateQuantity: (productId: number, quantity: number) => void;
    clearCart: () => void;
    getCart: () => Cart;
    getItemCount: () => number;
    getTotal: () => number;
}

const getStorage = () => {
    if (typeof window === 'undefined') {
        return {
            getItem: () => null,
            setItem: () => {},
            removeItem: () => {},
        };
    }
    return localStorage;
};

export const useCartStore = create<CartState>()(
    persist(
        (set, get) => ({
            items: [],
            addItem: (product, quantity = 1) => {
                set((state) => {
                    const existingItem = state.items.find((item) => item.product.id === product.id);
                    if (existingItem) {
                        return {
                            items: state.items.map((item) =>
                                item.product.id === product.id
                                    ? { ...item, quantity: item.quantity + quantity }
                                    : item
                            ),
                        };
                    }
                    return {
                        items: [...state.items, { product, quantity }],
                    };
                });
            },
            removeItem: (productId) => {
                set((state) => ({
                    items: state.items.filter((item) => item.product.id !== productId),
                }));
            },
            updateQuantity: (productId, quantity) => {
                if (quantity <= 0) {
                    get().removeItem(productId);
                    return;
                }
                set((state) => ({
                    items: state.items.map((item) =>
                        item.product.id === productId ? { ...item, quantity } : item
                    ),
                }));
            },
            clearCart: () => set({ items: [] }),
            getCart: () => {
                const state = get();
                return {
                    items: state.items || [],
                    total: state.getTotal(),
                    itemCount: state.getItemCount(),
                };
            },
            getItemCount: () => {
                const items = get().items || [];
                return items.reduce((sum, item) => sum + item.quantity, 0);
            },
            getTotal: () => {
                const items = get().items || [];
                return items.reduce((sum, item) => sum + item.product.price * item.quantity, 0);
            },
        }),
        {
            name: 'cart-storage',
            storage: createJSONStorage(() => getStorage()),
        }
    )
);

