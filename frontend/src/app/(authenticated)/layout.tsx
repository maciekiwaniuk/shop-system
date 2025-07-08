import ProtectedRoute from '@/components/ProtectedRoute';

export default function AuthenticatedLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    return (
        <ProtectedRoute requireAuth={true}>
            {children}
        </ProtectedRoute>
    );
} 