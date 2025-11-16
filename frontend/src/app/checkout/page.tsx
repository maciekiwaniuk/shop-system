'use client';

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { useCartStore } from '@/lib/store/cartStore';
import { useAuthStore } from '@/lib/store/authStore';
import { ordersApi } from '@/lib/api/orders';
import { Button } from '@/components/ui/Button';
import { formatPrice } from '@/lib/utils/format';
import { ArrowLeft } from 'lucide-react';
import { toast } from 'react-hot-toast';
import { useRef } from 'react';

export default function CheckoutPage() {
    const router = useRouter();
    const items = useCartStore((state) => state.items || []);
    const clearCart = useCartStore((state) => state.clearCart);
    const getTotal = useCartStore((state) => state.getTotal);
    const getItemCount = useCartStore((state) => state.getItemCount);
    const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
    const hasHydrated = useAuthStore((state) => state.hasHydrated);
    const [isLoading, setIsLoading] = useState(false);
    const isNavigatingRef = useRef(false);

    const cartItems = Array.isArray(items) ? items : [];
    const cartTotal = getTotal();
    const cartItemCount = getItemCount();

    useEffect(() => {
        if (!hasHydrated) return;
        if (!isAuthenticated) {
            router.replace('/login?redirect=/checkout');
            return;
        }
        if (!isNavigatingRef.current && cartItems.length === 0) {
            router.replace('/cart');
        }
    }, [hasHydrated, cartItems.length, isAuthenticated, router]);

    const handlePlaceOrder = async () => {
        if (cartItems.length === 0) {
            toast.error('Your cart is empty');
            return;
        }

        setIsLoading(true);
        try {
            const products = cartItems.map((item) => ({
                id: item.product.id,
                quantity: item.quantity,
                pricePerPiece: item.product.price,
            }));

            const response = await ordersApi.create(products);
            if (response.success) {
                toast.success('Order created successfully!');
                // Redirect to the newly created order detail
                const orderId = response.data?.id;
                if (orderId) {
                    isNavigatingRef.current = true;
                    router.push(`/orders/${orderId}`);
                    // Clear the cart after starting navigation to avoid redirecting back to /cart
                    clearCart();
                } else {
                    isNavigatingRef.current = true;
                    router.push('/orders');
                    clearCart();
                }
            } else {
                if (response.errors) {
                    Object.entries(response.errors).forEach(([field, message]) => {
                        toast.error(String(message));
                    });
                } else {
                    toast.error(response.message || 'Failed to create order');
                }
            }
        } catch (error: any) {
            console.error('Checkout error:', error);
            if (error?.response?.data) {
                const errorData = error.response.data;
                if (errorData.errors) {
                    Object.entries(errorData.errors).forEach(([field, message]) => {
                        toast.error(String(message));
                    });
                } else if (errorData.message) {
                    toast.error(errorData.message);
                } else {
                    toast.error('Failed to create order');
                }
            } else {
                toast.error('An error occurred while creating your order');
            }
        } finally {
            setIsLoading(false);
        }
    };

    if (!hasHydrated || cartItems.length === 0) {
        return null;
    }

    return (
        <div className="min-h-screen bg-gray-50">
            <div className="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
                <Link
                    href="/cart"
                    className="mb-6 inline-flex items-center gap-2 text-gray-600 hover:text-gray-900"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to Cart
                </Link>

                <div className="mx-auto max-w-4xl">
                    <h1 className="mb-8 text-3xl font-bold text-gray-900">Checkout</h1>

                    <div className="grid gap-8 lg:grid-cols-3">
                        {/* Order Review */}
                        <div className="lg:col-span-2">
                            <div className="rounded-lg bg-white p-6 shadow-sm">
                                <h2 className="mb-6 text-xl font-semibold text-gray-900">Review Your Order</h2>
                                <div className="space-y-4">
                                    {cartItems
                                        .filter((item) => item && item.product && item.product.id)
                                        .map((item) => (
                                        <div
                                            key={item.product.id}
                                            className="flex items-center gap-4 border-b border-gray-200 pb-4 last:border-0 last:pb-0"
                                        >
                                            <div className="flex h-16 w-16 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg border-2 border-gray-200 bg-gray-50">
                                                <span className="text-2xl opacity-60">📦</span>
                                            </div>
                                            <div className="flex-1">
                                                <h3 className="font-semibold text-gray-900">{item.product.name}</h3>
                                                <p className="text-sm text-gray-600">
                                                    {formatPrice(item.product.price)} × {item.quantity}
                                                </p>
                                            </div>
                                            <div className="text-right">
                                                <p className="font-semibold text-gray-900">
                                                    {formatPrice(item.product.price * item.quantity)}
                                                </p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Order Summary */}
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
                                    onClick={handlePlaceOrder}
                                    className="mt-6 w-full"
                                    size="lg"
                                    isLoading={isLoading}
                                >
                                    Place Order
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

