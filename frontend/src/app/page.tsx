'use client';

import { useState, useEffect, useCallback, useRef, Suspense } from 'react';
import { useSearchParams } from 'next/navigation';
import { ProductGrid } from '@/components/product/ProductGrid';
import { LoadingSpinner } from '@/components/shared/LoadingSpinner';
import { productsApi } from '@/lib/api/products';
import { Product } from '@/types/product';
import { toast } from 'react-hot-toast';

function HomePageContent() {
    const searchParams = useSearchParams();
    const searchPhrase = searchParams.get('search');

    const [products, setProducts] = useState<Product[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [offset, setOffset] = useState(0);
    const [hasMore, setHasMore] = useState(true);
    const [isLoadingMore, setIsLoadingMore] = useState(false);
    const sentinelRef = useRef<HTMLDivElement>(null);
    const loadingRef = useRef(false); // Prevent duplicate requests
    const offsetRef = useRef(0); // Track current offset accurately

    const loadProducts = useCallback(async (currentOffset: number, search?: string | null) => {
        if (loadingRef.current) return; // Prevent duplicate requests
        loadingRef.current = true;

        try {
            if (search) {
                const response = await productsApi.search(search);
                if (response.success && response.data) {
                    const productsList = Array.isArray(response.data) ? response.data : [];
                    setProducts(productsList);
                    setHasMore(false);
                } else {
                    toast.error(response.message || 'Failed to search products');
                }
            } else {
                const response = await productsApi.getPaginated({
                    offset: currentOffset,
                    limit: 12,
                });

                if (response.success && response.data) {
                    // Handle both array response and PaginatedProducts object
                    let productsList: Product[] = [];
                    let hasMoreProducts = false;

                    if (Array.isArray(response.data)) {
                        // Backend returns array directly
                        productsList = response.data;
                        hasMoreProducts = productsList.length === 12;
                    } else if (response.data.products && Array.isArray(response.data.products)) {
                        // Backend returns PaginatedProducts object
                        productsList = response.data.products;
                        const paginatedData = response.data;
                        hasMoreProducts =
                            productsList.length === 12 &&
                            paginatedData.offset !== undefined &&
                            paginatedData.total !== undefined &&
                            paginatedData.offset + productsList.length < paginatedData.total;
                    }

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
                    setHasMore(hasMoreProducts);
                } else {
                    toast.error(response.message || 'Failed to load products');
                }
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
        setIsLoading(true);
        setOffset(0);
        offsetRef.current = 0;
        setProducts([]);
        loadProducts(0, searchPhrase);
    }, [searchPhrase, loadProducts]);

    const loadMore = useCallback(() => {
        if (!isLoadingMore && hasMore && !searchPhrase && !loadingRef.current) {
            setIsLoadingMore(true);
            const nextOffset = offsetRef.current;
            setOffset(nextOffset);
            loadProducts(nextOffset);
        }
    }, [isLoadingMore, hasMore, searchPhrase, loadProducts]);

    // Infinite scroll with IntersectionObserver
    useEffect(() => {
        if (!sentinelRef.current || searchPhrase) return;

        const observer = new IntersectionObserver(
            (entries) => {
                const [entry] = entries;
                if (entry.isIntersecting && hasMore && !isLoadingMore && !isLoading) {
                    loadMore();
                }
            },
            { rootMargin: '200px' } // Trigger 200px before reaching the sentinel
        );

        observer.observe(sentinelRef.current);

        return () => {
            if (sentinelRef.current) {
                observer.unobserve(sentinelRef.current);
            }
        };
    }, [hasMore, isLoadingMore, isLoading, searchPhrase, loadMore]);

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Main Content */}
            <div className="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
                {/* Search Results Header */}
                {searchPhrase && (
                    <div className="mb-8">
                        <h1 className="text-3xl font-bold text-gray-900">
                            Search Results
                        </h1>
                        <p className="mt-2 text-gray-600">
                            Found <span className="font-semibold text-gray-900">{products.length}</span>{' '}
                            {products.length === 1 ? 'product' : 'products'} for "{searchPhrase}"
                        </p>
                    </div>
                )}

                {/* Products Grid */}
                <ProductGrid products={products} isLoading={isLoading} />

                {/* Infinite Scroll Sentinel & Loading Indicator */}
                {!searchPhrase && !isLoading && (
                    <>
                        {hasMore && (
                            <div ref={sentinelRef} className="mt-12 flex justify-center">
                                {isLoadingMore && (
                                    <div className="flex items-center gap-2 text-gray-600">
                                        <LoadingSpinner size="md" />
                                        <span>Loading more products...</span>
                                    </div>
                                )}
                            </div>
                        )}
                        {!hasMore && products.length > 0 && (
                            <div className="mt-12 text-center text-gray-500">
                                <p>You've reached the end of the products list</p>
                            </div>
                        )}
                    </>
                )}
            </div>
        </div>
    );
}

export default function HomePage() {
    return (
        <Suspense fallback={
            <div className="flex items-center justify-center py-12">
                <LoadingSpinner size="lg" />
            </div>
        }>
            <HomePageContent />
        </Suspense>
    );
}

