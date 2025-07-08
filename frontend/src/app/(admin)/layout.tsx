import ProtectedRoute from '@/components/ProtectedRoute';

export default function AdminLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    return (
        <ProtectedRoute requireAuth={true} requireAdmin={true}>
            {children}
        </ProtectedRoute>
    );
} 