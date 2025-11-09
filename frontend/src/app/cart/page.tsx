'use client';

import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { useCartStore } from '@/lib/store/cartStore';
import { Button } from '@/components/ui/Button';
import { formatPrice } from '@/lib/utils/format';
import { ShoppingCart, Plus, Minus, Trash2, ArrowLeft } from 'lucide-react';
import { EmptyState } from '@/components/shared/EmptyState';
import { toast } from 'react-hot-toast';

export default function CartPage() {
    const router = useRouter();
    const items = useCartStore((state) => state.items || []);
    const updateQuantity = useCartStore((state) => state.updateQuantity);
    const removeItem = useCartStore((state) => state.removeItem);
    const clearCart = useCartStore((state) => state.clearCart);
    const getTotal = useCartStore((state) => state.getTotal);
    const getItemCount = useCartStore((state) => state.getItemCount);

    const handleQuantityChange = (productId: number, newQuantity: number) => {
        if (newQuantity <= 0) {
            removeItem(productId);
            toast.success('Item removed from cart');
        } else {
            updateQuantity(productId, newQuantity);
        }
    };

    const handleRemoveItem = (productId: number) => {
        removeItem(productId);
        toast.success('Item removed from cart');
    };

    // Calculate cart values
    const cartItems = Array.isArray(items) ? items : [];
    const cartTotal = getTotal();
    const cartItemCount = getItemCount();

    if (!cartItems || cartItems.length === 0) {
        return (
            <div className="min-h-screen bg-gray-50">
                <div className="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
                    <Link
                        href="/"
                        className="mb-6 inline-flex items-center gap-2 text-gray-600 hover:text-gray-900"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Continue Shopping
                    </Link>
                    <EmptyState
                        title="Your cart is empty"
                        description="Add some products to your cart to get started"
                        action={
                            <Link href="/">
                                <Button>Browse Products</Button>
                            </Link>
                        }
                    />
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gray-50">
            <div className="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-3xl font-bold text-gray-900">Shopping Cart</h1>
                    <Link
                        href="/"
                        className="text-gray-600 hover:text-gray-900"
                    >
                        Continue Shopping
                    </Link>
                </div>

                <div className="grid gap-8 lg:grid-cols-3">
                    {/* Cart Items */}
                    <div className="lg:col-span-2">
                        <div className="space-y-4 rounded-lg bg-white p-6 shadow-sm">
                            {cartItems
                                .filter((item) => item && item.product && item.product.id)
                                .map((item) => (
                                <div
                                    key={item.product.id}
                                    className="flex items-center gap-4 border-b border-gray-200 pb-4 last:border-0 last:pb-0"
                                >
                                    <Link
                                        href={`/products/${item.product.slug}`}
                                        className="flex h-20 w-20 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg border-2 border-gray-200 bg-gray-50"
                                    >
                                        <span className="text-3xl opacity-60">📦</span>
                                    </Link>

                                    <div className="flex-1">
                                        <Link
                                            href={`/products/${item.product.slug}`}
                                            className="font-semibold text-gray-900 hover:text-gray-600"
                                        >
                                            {item.product.name}
                                        </Link>
                                        <p className="text-sm text-gray-600">{formatPrice(item.product.price)}</p>
                                    </div>

                                    <div className="flex items-center gap-3">
                                        <button
                                            onClick={() => handleQuantityChange(item.product.id, item.quantity - 1)}
                                            className="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-50"
                                        >
                                            <Minus className="h-4 w-4" />
                                        </button>
                                        <span className="min-w-[2rem] text-center font-semibold">{item.quantity}</span>
                                        <button
                                            onClick={() => handleQuantityChange(item.product.id, item.quantity + 1)}
                                            className="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-50"
                                        >
                                            <Plus className="h-4 w-4" />
                                        </button>
                                    </div>

                                    <div className="text-right">
                                        <p className="font-semibold text-gray-900">
                                            {formatPrice(item.product.price * item.quantity)}
                                        </p>
                                    </div>

                                    <button
                                        onClick={() => handleRemoveItem(item.product.id)}
                                        className="text-red-600 hover:text-red-700"
                                        aria-label="Remove item"
                                    >
                                        <Trash2 className="h-5 w-5" />
                                    </button>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Cart Summary */}
                    <div className="lg:col-span-1">
                        <div className="rounded-lg bg-white p-6 shadow-sm">
                            <h2 className="mb-4 text-xl font-semibold text-gray-900">Order Summary</h2>
                            <div className="space-y-3 pb-4">
                                <div className="flex justify-between text-sm">
                                    <span className="text-gray-600">Subtotal</span>
                                    <span className="font-medium text-gray-900">{formatPrice(cartTotal)}</span>
                                </div>
                                <div className="flex justify-between text-sm">
                                    <span className="text-gray-600">Items</span>
                                    <span className="font-medium text-gray-900">{cartItemCount}</span>
                                </div>
                            </div>
                            <div className="mt-4 flex justify-between border-t border-gray-200 pt-4">
                                <span className="text-lg font-semibold text-gray-900">Total</span>
                                <span className="text-lg font-bold text-gray-900">{formatPrice(cartTotal)}</span>
                            </div>
                            <Button
                                onClick={() => router.push('/checkout')}
                                className="mt-6 w-full"
                                size="lg"
                            >
                                Proceed to Checkout
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

