'use client';

import { useAuth } from '@/contexts/AuthContext';
import { useRouter } from 'next/navigation';
import { useEffect, ReactNode } from 'react';

interface ProtectedRouteProps {
    children: ReactNode;
    requireAuth?: boolean;
    requireAdmin?: boolean;
    redirectTo?: string;
}

export default function ProtectedRoute({ 
    children, 
    requireAuth = true, 
    requireAdmin = false, 
    redirectTo = '/login' 
}: ProtectedRouteProps) {
    const { isAuthenticated, isAdmin, isLoading } = useAuth();
    const router = useRouter();

    useEffect(() => {
        if (!isLoading) {
            if (requireAuth && !isAuthenticated) {
                router.push(redirectTo);
                return;
            }

            if (requireAdmin && !isAdmin) {
                router.push('/');
                return;
            }
        }
    }, [isAuthenticated, isAdmin, isLoading, requireAuth, requireAdmin, redirectTo, router]);

    if (isLoading) {
        return (
            <div className="min-h-screen flex items-center justify-center">
                <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-emerald-600"></div>
            </div>
        );
    }

    if (requireAuth && !isAuthenticated) {
        return null;
    }

    if (requireAdmin && !isAdmin) {
        return null;
    }

    return <>{children}</>;
} 