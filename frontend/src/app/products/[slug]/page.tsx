'use client';

import { useState, useEffect, Suspense } from 'react';
import { useParams, useRouter } from 'next/navigation';
import Link from 'next/link';
import { productsApi } from '@/lib/api/products';
import { useCartStore } from '@/lib/store/cartStore';
import { Product } from '@/types/product';
import { Button } from '@/components/ui/Button';
import { LoadingSpinner } from '@/components/shared/LoadingSpinner';
import { formatPrice } from '@/lib/utils/format';
import { ShoppingCart, ArrowLeft } from 'lucide-react';
import { toast } from 'react-hot-toast';

function ProductDetailContent() {
    const params = useParams();
    const router = useRouter();
    const slug = params.slug as string;
    const addItem = useCartStore((state) => state.addItem);

    const [product, setProduct] = useState<Product | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [quantity, setQuantity] = useState(1);

    useEffect(() => {
        const loadProduct = async () => {
            try {
                const response = await productsApi.getBySlug(slug);
                if (response.success && response.data) {
                    setProduct(response.data);
                } else {
                    toast.error(response.message || 'Product not found');
                    router.push('/');
                }
            } catch (error) {
                console.error('Error loading product:', error);
                toast.error('Failed to load product');
                router.push('/');
            } finally {
                setIsLoading(false);
            }
        };

        if (slug) {
            loadProduct();
        }
    }, [slug, router]);

    const handleAddToCart = () => {
        if (product) {
            addItem(product, quantity);
            toast.success(`Added ${quantity} ${quantity === 1 ? 'item' : 'items'} to cart`);
        }
    };

    const decreaseQuantity = () => {
        if (quantity > 1) {
            setQuantity(quantity - 1);
        }
    };

    const increaseQuantity = () => {
        setQuantity(quantity + 1);
    };

    if (isLoading) {
        return (
            <div className="flex min-h-screen items-center justify-center">
                <LoadingSpinner size="lg" />
            </div>
        );
    }

    if (!product) {
        return null;
    }

    return (
        <div className="min-h-screen bg-gray-50">
            <div className="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
                <Link
                    href="/"
                    className="mb-6 inline-flex items-center gap-2 text-gray-600 hover:text-gray-900"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to products
                </Link>

                <div className="mx-auto max-w-4xl">
                    <div className="grid gap-8 lg:grid-cols-2">
                        {/* Product Image */}
                        <div className="aspect-square w-full overflow-hidden rounded-xl border-2 border-gray-200 bg-white">
                            <div className="flex h-full items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                                <span className="text-8xl opacity-60">📦</span>
                            </div>
                        </div>

                        {/* Product Info */}
                        <div className="flex flex-col">
                            <h1 className="mb-4 text-4xl font-bold text-gray-900">{product.name}</h1>

                            <div className="mb-6">
                                <p className="text-3xl font-bold text-gray-900">{formatPrice(product.price)}</p>
                            </div>

                            {/* Quantity Selector */}
                            <div className="mb-6">
                                <label className="mb-2 block text-sm font-medium text-gray-700">Quantity</label>
                                <div className="flex items-center gap-3">
                                    <button
                                        onClick={decreaseQuantity}
                                        disabled={quantity <= 1}
                                        className="flex h-10 w-10 items-center justify-center rounded-lg border-2 border-gray-300 bg-white font-semibold text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        −
                                    </button>
                                    <span className="min-w-[3rem] text-center text-lg font-semibold">{quantity}</span>
                                    <button
                                        onClick={increaseQuantity}
                                        className="flex h-10 w-10 items-center justify-center rounded-lg border-2 border-gray-300 bg-white font-semibold text-gray-700 transition-colors hover:bg-gray-50"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>

                            {/* Add to Cart Button */}
                            <Button
                                onClick={handleAddToCart}
                                className="mb-4 w-full"
                                size="lg"
                            >
                                <ShoppingCart className="mr-2 h-5 w-5" />
                                Add to Cart
                            </Button>

                            {/* Product Details */}
                            <div className="mt-8 border-t border-gray-200 pt-6">
                                <h2 className="mb-4 text-xl font-semibold text-gray-900">Product Details</h2>
                                <dl className="space-y-3">
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Price</dt>
                                        <dd className="mt-1 text-sm text-gray-900">{formatPrice(product.price)}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Created</dt>
                                        <dd className="mt-1 text-sm text-gray-900">
                                            {new Date(product.createdAt).toLocaleDateString()}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default function ProductDetailPage() {
    return (
        <Suspense fallback={
            <div className="flex min-h-screen items-center justify-center">
                <LoadingSpinner size="lg" />
            </div>
        }>
            <ProductDetailContent />
        </Suspense>
    );
}

