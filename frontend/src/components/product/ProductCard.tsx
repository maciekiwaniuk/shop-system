'use client';

import Link from 'next/link';
import { formatPrice } from '@/lib/utils/format';
import { Product } from '@/types/product';
import { useCartStore } from '@/lib/store/cartStore';
import { ShoppingCart } from 'lucide-react';
import { useState } from 'react';
import { toast } from 'react-hot-toast';

interface ProductCardProps {
    product: Product;
}

export function ProductCard({ product }: ProductCardProps) {
    const addItem = useCartStore((state) => state.addItem);
    const [isHovered, setIsHovered] = useState(false);

    const handleAddToCart = (e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();
        addItem(product, 1);
        toast.success('Added to cart');
    };

    return (
        <div
            className="group relative flex flex-col overflow-hidden rounded-xl border-2 border-gray-200 bg-white shadow-md transition-all duration-300 hover:border-gray-300 hover:shadow-xl"
            onMouseEnter={() => setIsHovered(true)}
            onMouseLeave={() => setIsHovered(false)}
        >
            {/* Product Image */}
            <Link href={`/products/${product.slug}`} className="relative block">
                <div className="relative aspect-square w-full overflow-hidden border-b-2 border-gray-100 bg-gradient-to-br from-gray-50 to-gray-100">
                    <div className="flex h-full items-center justify-center transition-transform duration-500 group-hover:scale-110">
                        <div className="flex h-full w-full items-center justify-center bg-white">
                            <span className="text-6xl opacity-60">📦</span>
                        </div>
                    </div>
                </div>
            </Link>

            {/* Product Info */}
            <div className="flex flex-1 flex-col p-5">
                <Link href={`/products/${product.slug}`} className="flex-1">
                    <h3 className="mb-2 line-clamp-2 text-base font-semibold text-gray-900 transition-colors group-hover:text-gray-600">
                        {product.name}
                    </h3>
                </Link>

                {/* Price */}
                <div className="mb-4 flex items-baseline gap-2">
                    <span className="text-2xl font-bold text-gray-900">
                        {formatPrice(product.price)}
                    </span>
                </div>

                {/* Add to Cart Button */}
                <button
                    onClick={handleAddToCart}
                    className="flex w-full items-center justify-center gap-2 rounded-lg bg-gray-900 px-4 py-3 text-sm font-medium text-white transition-all duration-200 hover:bg-gray-800 active:scale-[0.98]"
                >
                    <ShoppingCart className="h-4 w-4" />
                    <span>Add to Cart</span>
                </button>
            </div>
        </div>
    );
}

