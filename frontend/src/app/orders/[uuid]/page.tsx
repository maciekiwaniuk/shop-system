'use client';

import { useState, useEffect, Suspense } from 'react';
import { useParams, useRouter } from 'next/navigation';
import Link from 'next/link';
import { useAuthStore } from '@/lib/store/authStore';
import { ordersApi } from '@/lib/api/orders';
import { paymentsApi } from '@/lib/api/payments';
import { Order } from '@/types/order';
import { Button } from '@/components/ui/Button';
import { LoadingSpinner } from '@/components/shared/LoadingSpinner';
import { formatPrice, formatDate } from '@/lib/utils/format';
import { ArrowLeft, CreditCard, X } from 'lucide-react';
import { toast } from 'react-hot-toast';

function OrderDetailContent() {
    const params = useParams();
    const router = useRouter();
    const uuid = params.uuid as string;
    const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
    const [order, setOrder] = useState<Order | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [isProcessing, setIsProcessing] = useState(false);

    useEffect(() => {
        if (!isAuthenticated) {
            router.push('/login');
            return;
        }

        const loadOrder = async () => {
            try {
                const response = await ordersApi.getByUuid(uuid);
                if (response.success && response.data) {
                    setOrder(response.data);
                } else {
                    toast.error(response.message || 'Order not found');
                    router.push('/orders');
                }
            } catch (error) {
                console.error('Error loading order:', error);
                toast.error('Failed to load order');
                router.push('/orders');
            } finally {
                setIsLoading(false);
            }
        };

        if (uuid) {
            loadOrder();
        }
    }, [uuid, isAuthenticated, router]);

    const getOrderStatus = (o: Order): string => {
        const updates = o.ordersStatusUpdates || [];
        const last = updates[updates.length - 1];
        return last?.status ?? 'waiting_for_payment';
    };

    const getOrderTotal = (o: Order): number => {
        const items = o.ordersProducts || [];
        return items.reduce((sum, item) => sum + (item.productPricePerPiece * item.productQuantity), 0);
    };

    const handlePay = async () => {
        if (!order) return;

        const orderId = order.id;
        if (!orderId) {
            toast.error('Invalid order ID');
            return;
        }

        setIsProcessing(true);
        try {
            // The transaction ID is the order UUID
            const response = await paymentsApi.completeTransaction(orderId);
            if (response.success) {
                toast.success('Payment completed successfully!');
                // Reload order to get updated status
                const orderResponse = await ordersApi.getByUuid(uuid);
                if (orderResponse.success && orderResponse.data) {
                    setOrder(orderResponse.data);
                }
            } else {
                toast.error(response.message || 'Failed to complete payment');
            }
        } catch (error: any) {
            console.error('Payment error:', error);
            if (error?.response?.data?.message) {
                toast.error(error.response.data.message);
            } else {
                toast.error('An error occurred while processing payment');
            }
        } finally {
            setIsProcessing(false);
        }
    };

    const handleCancel = async () => {
        if (!order) return;

        const orderId = order.id;
        if (!orderId) {
            toast.error('Invalid order ID');
            return;
        }

        setIsProcessing(true);
        try {
            // The transaction ID is the order UUID
            const response = await paymentsApi.cancelTransaction(orderId);
            if (response.success) {
                toast.success('Transaction cancelled successfully!');
                // Reload order to get updated status
                const orderResponse = await ordersApi.getByUuid(uuid);
                if (orderResponse.success && orderResponse.data) {
                    setOrder(orderResponse.data);
                }
            } else {
                toast.error(response.message || 'Failed to cancel transaction');
            }
        } catch (error: any) {
            console.error('Cancel error:', error);
            if (error?.response?.data?.message) {
                toast.error(error.response.data.message);
            } else {
                toast.error('An error occurred while cancelling transaction');
            }
        } finally {
            setIsProcessing(false);
        }
    };

    if (!isAuthenticated) {
        return null;
    }

    if (isLoading) {
        return (
            <div className="flex min-h-screen items-center justify-center">
                <LoadingSpinner size="lg" />
            </div>
        );
    }

    if (!order) {
        return null;
    }

    const status = getOrderStatus(order);
    const canPay = status === 'waiting_for_payment';
    const canCancel = status === 'waiting_for_payment';
    const total = getOrderTotal(order);

    return (
        <div className="min-h-screen bg-gray-50">
            <div className="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
                <Link
                    href="/orders"
                    className="mb-6 inline-flex items-center gap-2 text-gray-600 hover:text-gray-900"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to Orders
                </Link>

                <div className="mx-auto max-w-4xl">
                    <div className="mb-6 flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">Order Details</h1>
                            <p className="mt-1 text-sm text-gray-600">Order #{(order.id || '').slice(0, 8)}</p>
                        </div>
                        <span
                            className={`inline-block rounded-full px-4 py-2 text-sm font-semibold ${
                                status === 'completed'
                                    ? 'bg-green-100 text-green-800'
                                    : status === 'canceled'
                                      ? 'bg-red-100 text-red-800'
                                      : 'bg-yellow-100 text-yellow-800'
                            }`}
                        >
                            {status.replaceAll('_', ' ')}
                        </span>
                    </div>

                    <div className="grid gap-6 lg:grid-cols-3">
                        {/* Order Items */}
                        <div className="lg:col-span-2">
                            <div className="rounded-lg bg-white p-6 shadow-sm">
                                <h2 className="mb-4 text-xl font-semibold text-gray-900">Order Items</h2>
                                {order.ordersProducts && order.ordersProducts.length > 0 ? (
                                    <div className="space-y-4">
                                        {order.ordersProducts.map((item, index) => (
                                            <div
                                                key={`${item.product?.id ?? 'noid'}-${index}`}
                                                className="flex items-center gap-4 border-b border-gray-200 pb-4 last:border-0 last:pb-0"
                                            >
                                                <div className="flex h-16 w-16 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg border-2 border-gray-200 bg-gray-50">
                                                    <span className="text-2xl opacity-60">📦</span>
                                                </div>
                                                <div className="flex-1">
                                                    <h3 className="font-semibold text-gray-900">
                                                        {item.product.name}
                                                    </h3>
                                                    <p className="text-sm text-gray-600">
                                                        {formatPrice(item.productPricePerPiece)} × {item.productQuantity}
                                                    </p>
                                                </div>
                                                <div className="text-right">
                                                    <p className="font-semibold text-gray-900">
                                                        {formatPrice(item.productPricePerPiece * item.productQuantity)}
                                                    </p>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-gray-600">No items found</p>
                                )}
                            </div>
                        </div>

                        {/* Order Summary & Actions */}
                        <div className="lg:col-span-1">
                            <div className="rounded-lg bg-white p-6 shadow-sm">
                                <h2 className="mb-4 text-xl font-semibold text-gray-900">Order Summary</h2>
                                <div className="space-y-3 pb-4">
                                    <div className="flex justify-between text-sm">
                                        <span className="text-gray-600">Subtotal</span>
                                        <span className="font-medium text-gray-900">
                                            {formatPrice(total)}
                                        </span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-gray-600">Status</span>
                                        <span className="font-medium text-gray-900">{status.replaceAll('_', ' ')}</span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-gray-600">Created</span>
                                        <span className="font-medium text-gray-900">
                                            {formatDate(order.createdAt)}
                                        </span>
                                    </div>
                                </div>
                                <div className="mt-4 flex justify-between border-t border-gray-200 pt-4">
                                    <span className="text-lg font-semibold text-gray-900">Total</span>
                                    <span className="text-lg font-bold text-gray-900">{formatPrice(total)}</span>
                                </div>

                                {/* Payment Actions */}
                                {(canPay || canCancel) && (
                                    <div className="mt-6 space-y-3">
                                        {canPay && (
                                            <Button
                                                onClick={handlePay}
                                                className="w-full"
                                                size="lg"
                                                isLoading={isProcessing}
                                                disabled={isProcessing}
                                            >
                                                <CreditCard className="mr-2 h-5 w-5" />
                                                Pay Now
                                            </Button>
                                        )}
                                        {canCancel && (
                                            <Button
                                                onClick={handleCancel}
                                                variant="outline"
                                                className="w-full"
                                                size="lg"
                                                isLoading={isProcessing}
                                                disabled={isProcessing}
                                            >
                                                <X className="mr-2 h-5 w-5" />
                                                Cancel Transaction
                                            </Button>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default function OrderDetailPage() {
    return (
        <Suspense fallback={
            <div className="flex min-h-screen items-center justify-center">
                <LoadingSpinner size="lg" />
            </div>
        }>
            <OrderDetailContent />
        </Suspense>
    );
}

