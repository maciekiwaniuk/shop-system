'use client';

import { useState, useEffect, useCallback, Suspense } from 'react';
import { useSearchParams } from 'next/navigation';
import { ProductGrid } from '@/components/product/ProductGrid';
import { LoadingSpinner } from '@/components/shared/LoadingSpinner';
import { Button } from '@/components/ui/Button';
import { productsApi } from '@/lib/api/products';
import { Product } from '@/types/product';
import { toast } from 'react-hot-toast';

function HomePageContent() {
    const searchParams = useSearchParams();
    const searchPhrase = searchParams.get('search');

    const [products, setProducts] = useState<Product[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [offset, setOffset] = useState(1);
    const [hasMore, setHasMore] = useState(true);
    const [isLoadingMore, setIsLoadingMore] = useState(false);

    const loadProducts = useCallback(async (currentOffset: number, search?: string | null) => {
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

                    if (currentOffset === 1) {
                        setProducts(productsList);
                    } else {
                        setProducts((prev) => [...prev, ...productsList]);
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
        }
    }, []);

    useEffect(() => {
        setIsLoading(true);
        setOffset(1);
        setProducts([]);
        loadProducts(1, searchPhrase);
    }, [searchPhrase, loadProducts]);

    const loadMore = () => {
        if (!isLoadingMore && hasMore && !searchPhrase) {
            setIsLoadingMore(true);
            const nextOffset = offset + 12;
            setOffset(nextOffset);
            loadProducts(nextOffset);
        }
    };

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

                {/* Load More Button */}
                {hasMore && !searchPhrase && !isLoading && (
                    <div className="mt-12 flex justify-center">
                        <Button
                            onClick={loadMore}
                            disabled={isLoadingMore}
                            isLoading={isLoadingMore}
                            variant="outline"
                            size="lg"
                            className="min-w-[200px] border-2 border-gray-300 px-8 py-3 font-semibold hover:border-gray-400 hover:bg-gray-50"
                        >
                            {isLoadingMore ? 'Loading...' : 'Load More Products'}
                        </Button>
                    </div>
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

