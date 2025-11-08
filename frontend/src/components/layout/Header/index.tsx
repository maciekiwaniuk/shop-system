'use client';

import Link from 'next/link';
import { SearchBar } from './SearchBar';
import { CartIcon } from './CartIcon';
import { UserMenu } from './UserMenu';

export function Header() {
    return (
        <header className="sticky top-0 z-50 w-full border-b border-gray-200 bg-white shadow-sm">
            <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex h-16 items-center justify-between gap-4">
                    <Link href="/" className="flex items-center gap-2 transition-opacity hover:opacity-80">
                        <span className="text-2xl font-bold tracking-tight text-gray-900">Shop</span>
                    </Link>

                    <div className="hidden flex-1 items-center justify-center px-4 md:flex">
                        <SearchBar />
                    </div>

                    <div className="flex items-center gap-3">
                        <div className="md:hidden">
                            <SearchBar />
                        </div>
                        <CartIcon />
                        <UserMenu />
                    </div>
                </div>
            </div>
        </header>
    );
}

