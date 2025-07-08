import type { Metadata } from 'next';
import './globals.css';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { AuthProvider } from '@/contexts/AuthContext';

export const metadata: Metadata = {
    title: 'Shop system',
    description: 'Simple shop system made for educational purposes and also as a showcase.',
};

export default function RootLayout({
    children,
}: Readonly<{
    children: React.ReactNode;
}>) {
    return (
        <html lang='en'>
            <body className="antialiased">
                <AuthProvider>
                    <Header />
                    {children}
                    <Footer />
                </AuthProvider>
            </body>
        </html>
    );
}
