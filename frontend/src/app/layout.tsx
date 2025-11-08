import type { Metadata } from 'next';
import { Toaster } from 'react-hot-toast';
import { Header } from '@/components/layout/Header';
import { Footer } from '@/components/layout/Footer';
import '@/styles/globals.css';

export const metadata: Metadata = {
    title: 'Shop - Modern E-commerce',
    description: 'A modern e-commerce platform built with Next.js',
};

export default function RootLayout({
    children,
}: Readonly<{
    children: React.ReactNode;
}>) {
    return (
        <html lang="en">
            <body className="antialiased">
                <div className="flex min-h-screen flex-col bg-gray-50">
                    <Header />
                    <main className="flex-1">{children}</main>
                    <Footer />
                </div>
                <Toaster 
                    position="top-center"
                    toastOptions={{
                        duration: 5000,
                        style: {
                            background: '#fff',
                            color: '#1f2937',
                            borderRadius: '0.5rem',
                            border: '1px solid #e5e7eb',
                            boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
                            padding: '16px',
                            fontSize: '14px',
                            maxWidth: '400px',
                        },
                    }}
                />
            </body>
        </html>
    );
}

