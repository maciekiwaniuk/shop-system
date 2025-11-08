'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { ShoppingCart } from 'lucide-react';
import { useCartStore } from '@/lib/store/cartStore';

export function CartIcon() {
    const items = useCartStore((state) => state.items || []);
    const [mounted, setMounted] = useState(false);

    useEffect(() => {
        setMounted(true);
    }, []);

    // Calculate item count from items array
    const itemCount = mounted ? items.reduce((sum, item) => sum + item.quantity, 0) : 0;

    return (
        <Link href="/cart" className="relative">
            <ShoppingCart className="h-6 w-6 text-gray-700" />
            {mounted && itemCount > 0 && (
                <span className="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">
                    {itemCount > 99 ? '99+' : itemCount}
                </span>
            )}
        </Link>
    );
}

