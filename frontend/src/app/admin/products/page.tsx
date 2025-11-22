'use client';

import { useState, useEffect, useCallback, useRef } from 'react';
import { useRouter, usePathname } from 'next/navigation';
import Link from 'next/link';
import { useAuthStore } from '@/lib/store/authStore';
import { useIsAdmin } from '@/lib/utils/auth';
import { productsApi } from '@/lib/api/products';
import { Product } from '@/types/product';
import { Button } from '@/components/ui/Button';
import { LoadingSpinner } from '@/components/shared/LoadingSpinner';
import { formatPrice } from '@/lib/utils/format';
import { Plus, Edit, Trash2 } from 'lucide-react';
import { toast } from 'react-hot-toast';

export default function AdminProductsPage() {
    const router = useRouter();
    const pathname = usePathname();
    const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
    const hasHydrated = useAuthStore((state) => state.hasHydrated);
    const isAdmin = useIsAdmin();
    const [products, setProducts] = useState<Product[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [isLoadingMore, setIsLoadingMore] = useState(false);
    const [hasMore, setHasMore] = useState(true);
    const sentinelRef = useRef<HTMLDivElement>(null);
    const loadingRef = useRef(false); // Prevent duplicate requests
    const offsetRef = useRef(0); // Track current offset accurately

    const loadProducts = useCallback(async (currentOffset: number) => {
        if (loadingRef.current) return; // Prevent duplicate requests
        loadingRef.current = true;

        try {
            const response = await productsApi.getPaginated({ offset: currentOffset, limit: 20 });
            if (response.success && response.data) {
                const productsList = Array.isArray(response.data)
                    ? response.data
                    : response.data.products || [];

                if (currentOffset === 0) {
                    setProducts(productsList);
                    offsetRef.current = productsList.length;
                } else {
                    setProducts((prev) => {
                        // Deduplicate by ID to prevent race conditions
                        const existingIds = new Set(prev.map(p => p.id));
                        const newProducts = productsList.filter(p => !existingIds.has(p.id));
                        return [...prev, ...newProducts];
                    });
                    offsetRef.current += productsList.length;
                }

                setHasMore(productsList.length === 20);
            } else {
                toast.error(response.message || 'Failed to load products');
            }
        } catch (error) {
            console.error('Error loading products:', error);
            toast.error('An error occurred while loading products');
        } finally {
            setIsLoading(false);
            setIsLoadingMore(false);
            loadingRef.current = false; // Reset loading flag
        }
    }, []);

    useEffect(() => {
        if (!hasHydrated) return;
        if (!isAuthenticated) {
            router.replace(`/login?redirect=${encodeURIComponent(pathname)}`);
            return;
        }
        if (!isAdmin) {
            toast.error('You do not have permission to access admin.');
            router.push('/');
            return;
        }

        offsetRef.current = 0;
        loadProducts(0);
    }, [hasHydrated, isAuthenticated, isAdmin, router, pathname, loadProducts]);

    const loadMore = useCallback(() => {
        if (!isLoadingMore && hasMore && !loadingRef.current) {
            setIsLoadingMore(true);
            const nextOffset = offsetRef.current;
            loadProducts(nextOffset);
        }
    }, [isLoadingMore, hasMore, loadProducts]);

    // Infinite scroll with IntersectionObserver
    useEffect(() => {
        if (!sentinelRef.current) return;

        const observer = new IntersectionObserver(
            (entries) => {
                const [entry] = entries;
                if (entry.isIntersecting && hasMore && !isLoadingMore && !isLoading) {
                    loadMore();
                }
            },
            { rootMargin: '200px' }
        );

        observer.observe(sentinelRef.current);

        return () => {
            if (sentinelRef.current) {
                observer.unobserve(sentinelRef.current);
            }
        };
    }, [hasMore, isLoadingMore, isLoading, loadMore]);

    const handleDelete = async (productId: number) => {
        if (!confirm('Are you sure you want to delete this product?')) {
            return;
        }

        try {
            const response = await productsApi.delete(productId);
            if (response.success) {
                toast.success('Product deleted successfully');
                setProducts(products.filter((p) => p.id !== productId));
            } else {
                toast.error(response.message || 'Failed to delete product');
            }
        } catch (error: any) {
            console.error('Error deleting product:', error);
            
            // Handle 401/403 errors specifically
            if (error?.response?.status === 401 || error?.response?.status === 403) {
                const errorMessage = error?.response?.data?.message || 'Access denied';
                if (errorMessage.toLowerCase().includes('access denied') || 
                    errorMessage.toLowerCase().includes('forbidden') ||
                    errorMessage.toLowerCase().includes('permission') ||
                    errorMessage.toLowerCase().includes('full authentication')) {
                    toast.error('You do not have permission to delete products. Admin access required.');
                } else {
                    toast.error('Authentication failed. Please log in again.');
                }
            } else if (error?.response?.data?.message) {
                toast.error(error.response.data.message);
            } else {
                toast.error('An error occurred while deleting product');
            }
        }
    };

    if (!hasHydrated) {
        return (
            <div className="flex min-h-screen items-center justify-center">
                <LoadingSpinner size="lg" />
            </div>
        );
    }

    if (!isAuthenticated || !isAdmin) {
        return null;
    }

    if (isLoading) {
        return (
            <div className="flex min-h-screen items-center justify-center">
                <LoadingSpinner size="lg" />
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gray-50">
            <div className="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-3xl font-bold text-gray-900">Manage Products</h1>
                    <Link href="/admin/products/create">
                        <Button>
                            <Plus className="mr-2 h-5 w-5" />
                            Create Product
                        </Button>
                    </Link>
                </div>

                <div className="rounded-lg bg-white shadow-sm">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="border-b border-gray-200 bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Name
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Slug
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Price
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200 bg-white">
                                {products.map((product) => (
                                    <tr key={product.id} className="hover:bg-gray-50">
                                        <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                            {product.name}
                                        </td>
                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                            {product.slug}
                                        </td>
                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                            {formatPrice(product.price)}
                                        </td>
                                        <td className="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                            <div className="flex items-center gap-2">
                                                <Link href={`/admin/products/${product.id}/edit`}>
                                                    <Button variant="ghost" size="sm">
                                                        <Edit className="h-4 w-4" />
                                                    </Button>
                                                </Link>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => handleDelete(product.id)}
                                                >
                                                    <Trash2 className="h-4 w-4 text-red-600" />
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Infinite Scroll Sentinel & Loading Indicator */}
                {!isLoading && (
                    <>
                        {hasMore && (
                            <div ref={sentinelRef} className="mt-8 flex justify-center">
                                {isLoadingMore && (
                                    <div className="flex items-center gap-2 text-gray-600">
                                        <LoadingSpinner size="md" />
                                        <span>Loading more products...</span>
                                    </div>
                                )}
                            </div>
                        )}
                        {!hasMore && products.length > 0 && (
                            <div className="mt-8 text-center text-gray-500">
                                <p>You've reached the end of the products list</p>
                            </div>
                        )}
                    </>
                )}
            </div>
        </div>
    );
}

