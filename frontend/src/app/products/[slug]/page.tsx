'use client';

import React, { useState, useEffect } from 'react';
import { useParams, useRouter } from 'next/navigation';
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
    data: Product;
}

const SkeletonProduct: React.FC = () => (
    <div className="animate-pulse">
        <div className="h-8 bg-gray-200 rounded w-1/2 mb-4"></div>
        <div className="h-6 bg-gray-200 rounded w-1/4 mb-2"></div>
        <div className="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
        <div className="h-4 bg-gray-200 rounded w-1/2"></div>
    </div>
);

export default function ProductPage() {
    const [product, setProduct] = useState<Product | null>(null);
    const [isLoading, setIsLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);
    const params = useParams();
    const router = useRouter();
    const slug = params.slug as string;

    useEffect(() => {
        const fetchProduct = async () => {
            setIsLoading(true);
            setError(null);

            try {
                const response = await fetch(`http://localhost/api/v1/products/show/${slug}`);
                const responseData: ApiResponse = await response.json();

                if (!response.ok) {
                    throw new Error('Server error, please try again later');
                }

                if (!responseData.success) {
                    throw new Error('Failed to fetch product details');
                }

                setProduct(responseData.data);
            } catch (err) {
                setError(err instanceof Error ? err.message : 'An error occurred');
                setProduct(null);
            } finally {
                setIsLoading(false);
            }
        };

        if (slug) {
            fetchProduct();
        }
    }, [slug]);

    return (
        <div className="min-h-screen bg-gray-50">
            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {isLoading && (
                    <div className="max-w-2xl mx-auto">
                        <SkeletonProduct />
                    </div>
                )}

                {error && (
                    <div className="max-w-2xl mx-auto bg-red-50 text-red-600 p-4 rounded-md">
                        {error}
                    </div>
                )}

                {product && !isLoading && !error && (
                    <div className="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
                        <h1 className="text-3xl font-bold text-gray-900 mb-4">{product.name}</h1>
                        <div className="space-y-4">
                            <p className="text-lg text-gray-600">
                                Price: <span className="font-semibold">${product.price.toFixed(2)}</span>
                            </p>
                            <p className="text-sm text-gray-500">
                                Product ID: <span className="font-medium">{product.id}</span>
                            </p>
                            <p className="text-sm text-gray-500">
                                Created: <span className="font-medium">{new Date(product.createdAt).toLocaleDateString()}</span>
                            </p>
                            <p className="text-sm text-gray-500">
                                Last Updated: <span className="font-medium">{new Date(product.updatedAt).toLocaleDateString()}</span>
                            </p>
                            <button
                                onClick={() => router.push('/')}
                                className="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                            >
                                Back to Products
                            </button>
                        </div>
                    </div>
                )}
            </main>
        </div>
    );
}