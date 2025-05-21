'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';

interface Product {
    id: number;
    name: string;
    slug: string;
    price: number;
    updatedAt: string;
    createdAt: string;
}

interface ApiResponse {
    success: boolean;
    data: Product[];
}

const SkeletonProduct: React.FC = () => (
    <div className="animate-pulse rounded-md bg-gray-100 p-4">
        <div className="h-48 w-full rounded-md bg-gray-200" />
        <div className="mt-4 space-y-2">
            <div className="h-4 w-3/4 rounded bg-gray-200" />
            <div className="h-3 w-1/2 rounded bg-gray-200" />
        </div>
    </div>
);

export default function Home() {
    const [products, setProducts] = useState<Product[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchProducts = async () => {
            try {
                const response = await fetch('http://localhost/api/v1/products/get-paginated?offset=1&limit=20');
                if (!response.ok) throw new Error('Failed to fetch products');

                const data: ApiResponse = await response.json();
                if (!data.success) throw new Error('API returned an error');

                setProducts(data.data);
            } catch (err) {
                console.log(err);
                setError(err instanceof Error ? err.message : 'An unexpected error occurred');
            } finally {
                setIsLoading(false);
            }
        };

        fetchProducts();
    }, []);

    const getRandomImage = (id: number) => `https://picsum.photos/seed/${id}-${Date.now()}/400/400.webp`;

    return (
        <div className="min-h-screen bg-gray-50">
            <main className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <h2 className="text-2xl font-bold text-gray-900">Customers Also Purchased</h2>

                {error && (
                    <div className="mt-6 rounded-md bg-red-50 p-4 text-center text-sm text-red-600">
                        {error}
                    </div>
                )}

                {isLoading && (
                    <div className="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        {Array(4).fill(0).map((_, index) => (
                            <SkeletonProduct key={index} />
                        ))}
                    </div>
                )}

                {!isLoading && !error && (
                    <div className="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        {products.map((product) => (
                            <Link
                                key={product.id}
                                href={`/products/${product.slug}`}
                                className="group rounded-md bg-white p-4 shadow-sm transition-shadow hover:shadow-md"
                            >
                                <img
                                    src={getRandomImage(product.id)}
                                    alt={product.name}
                                    className="h-48 w-full rounded-md object-cover transition-opacity group-hover:opacity-90"
                                />
                                <div className="mt-4 flex justify-between">
                                    <div>
                                        <h3 className="text-sm font-semibold text-gray-900">{product.name}</h3>
                                        <p className="text-xs text-gray-500">ID: {product.id}</p>
                                    </div>
                                    <p className="text-sm font-medium text-gray-900">${product.price.toFixed(2)}</p>
                                </div>
                            </Link>
                        ))}
                    </div>
                )}
            </main>
        </div>
    );
}