'use client';

import { useState, useEffect, Suspense } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import Link from 'next/link';
import { useAuthStore } from '@/lib/store/authStore';
import { ordersApi } from '@/lib/api/orders';
import { Order } from '@/types/order';
import { Button } from '@/components/ui/Button';
import { LoadingSpinner } from '@/components/shared/LoadingSpinner';
import { EmptyState } from '@/components/shared/EmptyState';
import { formatPrice, formatDate } from '@/lib/utils/format';
import { toast } from 'react-hot-toast';
import { useIsAdmin } from '@/lib/utils/auth';

function OrdersListContent() {
    const router = useRouter();
    const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
    const token = useAuthStore((state) => state.token);
    const isAdmin = useIsAdmin();
    const [orders, setOrders] = useState<Order[]>([]);
    const [allOrders, setAllOrders] = useState<Order[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [cursor, setCursor] = useState<string | undefined>();
    const [allCursor, setAllCursor] = useState<string | undefined>();
    const [hasMore, setHasMore] = useState(true);
    const [hasMoreAll, setHasMoreAll] = useState(true);

    useEffect(() => {
        if (!isAuthenticated || !token) {
            router.push('/login');
            return;
        }

        const loadOrders = async () => {
            try {
                const response = await ordersApi.getMyPaginated(cursor, 10);
                if (response.success && response.data) {
                    if (Array.isArray(response.data)) {
                        setOrders(response.data);
                        setHasMore(response.data.length === 10);
                    } else if (response.data.orders) {
                        setOrders(response.data.orders);
                        setHasMore(response.data.orders.length === 10);
                        setCursor(response.data.cursor);
                    }
                } else {
                    toast.error(response.message || 'Failed to load orders');
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                toast.error('An error occurred while loading orders');
                // If unauthorized, go to login
                // @ts-ignore
                if (error?.response?.status === 401) {
                    router.push('/login');
                }
            } finally {
                setIsLoading(false);
            }
        };

        loadOrders();
    }, [isAuthenticated, token, router, cursor]);

    useEffect(() => {
        if (!isAuthenticated || !token || !isAdmin) return;
        const loadAllOrders = async () => {
            try {
                const response = await ordersApi.getPaginated(allCursor, 10);
                if (response.success && response.data) {
                    if (Array.isArray(response.data)) {
                        setAllOrders(response.data);
                        setHasMoreAll(response.data.length === 10);
                    } else if (response.data.orders) {
                        setAllOrders(response.data.orders);
                        setHasMoreAll(response.data.orders.length === 10);
                        setAllCursor(response.data.cursor);
                    }
                } else {
                    toast.error(response.message || 'Failed to load all orders');
                }
            } catch (error) {
                console.error('Error loading all orders:', error);
                toast.error('An error occurred while loading all orders');
            }
        };
        loadAllOrders();
    }, [isAuthenticated, token, isAdmin, allCursor]);

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

    const getOrderStatus = (o: Order): string => {
        const updates = o.ordersStatusUpdates || [];
        const last = updates[updates.length - 1];
        return last?.status ?? 'waiting_for_payment';
    };

    const getOrderTotal = (o: Order): number => {
        const items = o.ordersProducts || [];
        return items.reduce((sum, item) => sum + (item.productPricePerPiece * item.productQuantity), 0);
    };

    return (
        <div className="min-h-screen bg-gray-50">
            <div className="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
                <h1 className="mb-8 text-3xl font-bold text-gray-900">My Orders</h1>

                {orders.length === 0 ? (
                    <EmptyState
                        title="No orders yet"
                        description="You haven't placed any orders yet"
                        action={
                            <Link href="/">
                                <Button>Browse Products</Button>
                            </Link>
                        }
                    />
                ) : (
                    <div className="space-y-4">
                        {orders
                            .filter((order) => order && (order.uuid || order.id))
                            .map((order) => {
                                const orderId = order.id || '';
                                const orderDisplayId = orderId ? orderId.slice(0, 8) : 'N/A';
                                const total = getOrderTotal(order);
                                const status = getOrderStatus(order);
                                return (
                                    <Link
                                        key={orderId}
                                        href={`/orders/${orderId}`}
                                        className="block rounded-lg border-2 border-gray-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md"
                                    >
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <h3 className="text-lg font-semibold text-gray-900">
                                                    Order #{orderDisplayId}
                                                </h3>
                                                <p className="mt-1 text-sm text-gray-600">
                                                    {order.createdAt ? formatDate(order.createdAt) : 'N/A'}
                                                </p>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-lg font-bold text-gray-900">
                                                    {formatPrice(total)}
                                                </p>
                                                <span
                                                    className={`mt-1 inline-block rounded-full px-3 py-1 text-xs font-semibold ${
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
                                        </div>
                                    </Link>
                                );
                            })}
                    </div>
                )}

                {isAdmin && (
                    <div className="mt-12">
                        <h2 className="mb-6 text-2xl font-bold text-gray-900">All Orders</h2>
                        {allOrders.length === 0 ? (
                            <div className="rounded-lg border-2 border-gray-200 bg-white p-6 text-sm text-gray-600">
                                No orders to display.
                            </div>
                        ) : (
                            <div className="space-y-4">
                                {allOrders
                                    .filter((order) => order && (order.uuid || order.id))
                                    .map((order) => {
                                        const orderId = order.id || '';
                                        const orderDisplayId = orderId ? orderId.slice(0, 8) : 'N/A';
                                        const total = getOrderTotal(order);
                                        const status = getOrderStatus(order);
                                        return (
                                            <Link
                                                key={`all-${orderId}`}
                                                href={`/orders/${orderId}`}
                                                className="block rounded-lg border-2 border-gray-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md"
                                            >
                                                <div className="flex items-center justify-between">
                                                    <div>
                                                        <h3 className="text-lg font-semibold text-gray-900">
                                                            Order #{orderDisplayId}
                                                        </h3>
                                                        <p className="mt-1 text-sm text-gray-600">
                                                            {order.createdAt ? formatDate(order.createdAt) : 'N/A'}
                                                        </p>
                                                    </div>
                                                    <div className="text-right">
                                                        <p className="text-lg font-bold text-gray-900">
                                                            {formatPrice(total)}
                                                        </p>
                                                        <span
                                                            className={`mt-1 inline-block rounded-full px-3 py-1 text-xs font-semibold ${
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
                                                </div>
                                            </Link>
                                        );
                                    })}
                            </div>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}

export default function OrdersPage() {
    return (
        <Suspense fallback={
            <div className="flex min-h-screen items-center justify-center">
                <LoadingSpinner size="lg" />
            </div>
        }>
            <OrdersListContent />
        </Suspense>
    );
}

