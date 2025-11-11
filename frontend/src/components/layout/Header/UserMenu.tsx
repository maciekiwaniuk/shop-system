'use client';

import Link from 'next/link';
import { useAuthStore } from '@/lib/store/authStore';
import { Button } from '@/components/ui/Button';
import { User, LogOut, Settings } from 'lucide-react';
import { useIsAdmin } from '@/lib/utils/auth';

export function UserMenu() {
    const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
    const logout = useAuthStore((state) => state.logout);
    const isAdmin = useIsAdmin();

    const handleLogout = () => {
        logout();
        window.location.href = '/';
    };

    if (!isAuthenticated) {
        return (
            <div className="flex items-center gap-2">
                <Link href="/login">
                    <Button variant="ghost" size="sm">
                        Login
                    </Button>
                </Link>
                <Link href="/register">
                    <Button variant="default" size="sm">
                        Register
                    </Button>
                </Link>
            </div>
        );
    }

    return (
        <div className="flex items-center gap-4">
            <Link href="/orders" className="flex items-center gap-2 text-gray-700 hover:text-gray-900">
                <User className="h-5 w-5" />
                <span className="hidden sm:inline">Orders</span>
            </Link>
            {isAdmin && (
                <Link href="/admin/products" className="flex items-center gap-2 text-gray-700 hover:text-gray-900">
                    <Settings className="h-5 w-5" />
                    <span className="hidden sm:inline">Admin</span>
                </Link>
            )}
            <Button variant="ghost" size="sm" onClick={handleLogout}>
                <LogOut className="mr-2 h-4 w-4" />
                <span className="hidden sm:inline">Logout</span>
            </Button>
        </div>
    );
}

