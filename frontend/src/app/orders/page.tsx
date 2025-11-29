'use client';

import { useState, useEffect, Suspense, useRef, useCallback } from 'react';
import { useRouter, usePathname, useSearchParams } from 'next/navigation';
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
    const pathname = usePathname();
    const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
    const hasHydrated = useAuthStore((state) => state.hasHydrated);
    const token = useAuthStore((state) => state.token);
    const logout = useAuthStore((state) => state.logout);
    const isAdmin = useIsAdmin();
    const [orders, setOrders] = useState<Order[]>([]);
    const [allOrders, setAllOrders] = useState<Order[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [cursor, setCursor] = useState<string | undefined>();
    const [allCursor, setAllCursor] = useState<string | undefined>();
    const [hasMore, setHasMore] = useState(false);
    const [hasMoreAll, setHasMoreAll] = useState(false);
    const [isLoadingMore, setIsLoadingMore] = useState(false);
    const [isLoadingMoreAll, setIsLoadingMoreAll] = useState(false);
    const loadingRef = useRef(false); // Prevent multiple simultaneous requests

    const handleAuthError = useCallback(() => {
        logout();
        toast.error('Session expired. Please log in again.');
        router.replace(`/login?redirect=${encodeURIComponent(pathname)}`);
    }, [logout, router, pathname]);

    useEffect(() => {
        if (!hasHydrated) return;
        if (!isAuthenticated || !token) {
            router.replace(`/login?redirect=${encodeURIComponent(pathname)}`);
            return;
        }

        if (loadingRef.current) return; // Prevent duplicate requests

        const loadOrders = async () => {
            loadingRef.current = true;
            try {
                const response = await ordersApi.getMyPaginated(undefined, 10);
                if (response.success && response.data) {
                    const ordersList = Array.isArray(response.data) ? response.data : response.data.orders || [];
                    setOrders(ordersList);
                    
                    // Set cursor to last order's ID if we got a full page
                    if (ordersList.length === 10) {
                        const lastOrder = ordersList[ordersList.length - 1];
                        const nextCursor = lastOrder?.id;
                        setCursor(nextCursor);
                        setHasMore(true);
                    } else {
                        setHasMore(false);
                    }
                } else {
                    toast.error(response.message || 'Failed to load orders');
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                // @ts-ignore
                if (error?.response?.status === 401) {
                    handleAuthError();
                } else {
                    toast.error('An error occurred while loading orders');
                }
            } finally {
                setIsLoading(false);
                loadingRef.current = false;
            }
        };

        loadOrders();
    }, [hasHydrated, isAuthenticated, token, router, pathname, handleAuthError]);

    const loadMoreOrders = async () => {
        if (!cursor || isLoadingMore) return;
        
        setIsLoadingMore(true);
        try {
            const response = await ordersApi.getMyPaginated(cursor, 10);
            if (response.success && response.data) {
                const ordersList = Array.isArray(response.data) ? response.data : response.data.orders || [];
                setOrders(prev => [...prev, ...ordersList]);
                
                // Set cursor to last order's ID if we got a full page
                if (ordersList.length === 10) {
                    const lastOrder = ordersList[ordersList.length - 1];
                    const nextCursor = lastOrder?.id;
                    setCursor(nextCursor);
                    setHasMore(true);
                } else {
                    setHasMore(false);
                    setCursor(undefined);
                }
            } else {
                toast.error(response.message || 'Failed to load more orders');
            }
        } catch (error) {
            console.error('Error loading more orders:', error);
            // @ts-ignore
            if (error?.response?.status === 401) {
                handleAuthError();
            } else {
                toast.error('An error occurred while loading more orders');
            }
        } finally {
            setIsLoadingMore(false);
        }
    };

    useEffect(() => {
        if (!hasHydrated || !isAuthenticated || !token || !isAdmin) return;
        
        const loadAllOrders = async () => {
            try {
                const response = await ordersApi.getPaginated(undefined, 10);
                if (response.success && response.data) {
                    const ordersList = Array.isArray(response.data) ? response.data : response.data.orders || [];
                    setAllOrders(ordersList);
                    
                    // Set cursor to last order's ID if we got a full page
                    if (ordersList.length === 10) {
                        const lastOrder = ordersList[ordersList.length - 1];
                        const nextCursor = lastOrder?.id;
                        setAllCursor(nextCursor);
                        setHasMoreAll(true);
                    } else {
                        setHasMoreAll(false);
                    }
                } else {
                    toast.error(response.message || 'Failed to load all orders');
                }
            } catch (error) {
                console.error('Error loading all orders:', error);
                // @ts-ignore
                if (error?.response?.status === 401) {
                    handleAuthError();
                } else {
                    toast.error('An error occurred while loading all orders');
                }
            }
        };
        loadAllOrders();
    }, [hasHydrated, isAuthenticated, token, isAdmin, handleAuthError]);

    const loadMoreAllOrders = async () => {
        if (!allCursor || isLoadingMoreAll) return;
        
        setIsLoadingMoreAll(true);
        try {
            const response = await ordersApi.getPaginated(allCursor, 10);
            if (response.success && response.data) {
                const ordersList = Array.isArray(response.data) ? response.data : response.data.orders || [];
                setAllOrders(prev => [...prev, ...ordersList]);
                
                // Set cursor to last order's ID if we got a full page
                if (ordersList.length === 10) {
                    const lastOrder = ordersList[ordersList.length - 1];
                    const nextCursor = lastOrder?.id;
                    setAllCursor(nextCursor);
                    setHasMoreAll(true);
                } else {
                    setHasMoreAll(false);
                    setAllCursor(undefined);
                }
            } else {
                toast.error(response.message || 'Failed to load more orders');
            }
        } catch (error) {
            console.error('Error loading more all orders:', error);
            // @ts-ignore
            if (error?.response?.status === 401) {
                handleAuthError();
            } else {
                toast.error('An error occurred while loading more orders');
            }
        } finally {
            setIsLoadingMoreAll(false);
        }
    };

    if (!hasHydrated) {
        return (
            <div className="flex min-h-screen items-center justify-center">
                <LoadingSpinner size="lg" />
            </div>
        );
    }

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
        if (updates.length === 0) return 'waiting_for_payment';
        // Backend returns statuses in descending order (newest first)
        // So we take the FIRST item, not the last
        return updates[0]?.status ?? 'waiting_for_payment';
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
                    <>
                        <div className="space-y-4">
                            {orders
                                .filter((order) => order && order.id)
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
                        {hasMore && cursor && (
                            <div className="mt-6 flex justify-center">
                                <Button
                                    onClick={loadMoreOrders}
                                    disabled={isLoadingMore}
                                    isLoading={isLoadingMore}
                                    variant="outline"
                                    size="lg"
                                    className="min-w-[200px] border-2 border-gray-300 px-8 py-3 font-semibold hover:border-gray-400 hover:bg-gray-50"
                                >
                                    {isLoadingMore ? 'Loading...' : 'Load More Orders'}
                                </Button>
                            </div>
                        )}
                    </>
                )}

                {isAdmin && (
                    <div className="mt-12">
                        <h2 className="mb-6 text-2xl font-bold text-gray-900">All Orders (Admin)</h2>
                        {allOrders.length === 0 ? (
                            <div className="rounded-lg border-2 border-gray-200 bg-white p-6 text-sm text-gray-600">
                                No orders to display.
                            </div>
                        ) : (
                            <>
                                <div className="space-y-4">
                                    {allOrders
                                        .filter((order) => order && order.id)
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
                                {hasMoreAll && allCursor && (
                                    <div className="mt-6 flex justify-center">
                                        <Button
                                            onClick={loadMoreAllOrders}
                                            disabled={isLoadingMoreAll}
                                            isLoading={isLoadingMoreAll}
                                            variant="outline"
                                            size="lg"
                                            className="min-w-[200px] border-2 border-gray-300 px-8 py-3 font-semibold hover:border-gray-400 hover:bg-gray-50"
                                        >
                                            {isLoadingMoreAll ? 'Loading...' : 'Load More Orders'}
                                        </Button>
                                    </div>
                                )}
                            </>
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

