'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { productsApi } from '@/lib/api/products';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { ArrowLeft } from 'lucide-react';
import { toast } from 'react-hot-toast';

const createProductSchema = z.object({
    name: z.string().min(1, 'Name is required'),
    slug: z.string().min(1, 'Slug is required'),
    price: z.number().positive('Price must be greater than 0'),
});

type CreateProductFormData = z.infer<typeof createProductSchema>;

export default function CreateProductPage() {
    const router = useRouter();
    const [isLoading, setIsLoading] = useState(false);

    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm<CreateProductFormData>({
        resolver: zodResolver(createProductSchema),
    });

    const onSubmit = async (data: CreateProductFormData) => {
        setIsLoading(true);
        try {
            const response = await productsApi.create(data);
            if (response.success) {
                toast.success('Product created successfully!');
                router.push('/admin/products');
            } else {
                if (response.errors) {
                    Object.entries(response.errors).forEach(([field, message]) => {
                        toast.error(String(message));
                    });
                } else {
                    toast.error(response.message || 'Failed to create product');
                }
            }
        } catch (error: any) {
            console.error('Create product error:', error);
            
            // Handle 401/403 errors specifically
            if (error?.response?.status === 401 || error?.response?.status === 403) {
                const errorMessage = error?.response?.data?.message || 'Access denied';
                console.error('Admin action failed:', {
                    status: error?.response?.status,
                    message: errorMessage,
                    url: error?.config?.url,
                });
                
                if (errorMessage.toLowerCase().includes('access denied') || 
                    errorMessage.toLowerCase().includes('forbidden') ||
                    errorMessage.toLowerCase().includes('permission')) {
                    toast.error('You do not have permission to create products. Admin access required.');
                } else if (errorMessage.toLowerCase().includes('full authentication') ||
                          errorMessage.toLowerCase().includes('authentication')) {
                    toast.error('Authentication failed. Your session may have expired. Please log in again.');
                    // Redirect after showing error
                    setTimeout(() => {
                        if (typeof window !== 'undefined') {
                            localStorage.removeItem('auth_token');
                            window.location.href = '/login';
                        }
                    }, 3000);
                } else {
                    toast.error('Authentication failed. Please check your credentials and try again.');
                }
            } else if (error?.response?.data) {
                const errorData = error.response.data;
                if (errorData.errors) {
                    Object.entries(errorData.errors).forEach(([field, message]) => {
                        toast.error(String(message));
                    });
                } else if (errorData.message) {
                    toast.error(errorData.message);
                } else {
                    toast.error('Failed to create product');
                }
            } else {
                toast.error('An error occurred while creating product');
            }
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-gray-50">
            <div className="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
                <Link
                    href="/admin/products"
                    className="mb-6 inline-flex items-center gap-2 text-gray-600 hover:text-gray-900"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to Products
                </Link>

                <div className="mx-auto max-w-2xl">
                    <h1 className="mb-8 text-3xl font-bold text-gray-900">Create Product</h1>

                    <div className="rounded-lg bg-white p-6 shadow-sm">
                        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
                            <div>
                                <label htmlFor="name" className="block text-sm font-medium text-gray-700">
                                    Name
                                </label>
                                <Input
                                    id="name"
                                    type="text"
                                    className="mt-1"
                                    {...register('name')}
                                />
                                {errors.name && (
                                    <p className="mt-1 text-sm text-red-600">{errors.name.message}</p>
                                )}
                            </div>

                            <div>
                                <label htmlFor="slug" className="block text-sm font-medium text-gray-700">
                                    Slug
                                </label>
                                <Input
                                    id="slug"
                                    type="text"
                                    className="mt-1"
                                    {...register('slug')}
                                />
                                {errors.slug && (
                                    <p className="mt-1 text-sm text-red-600">{errors.slug.message}</p>
                                )}
                            </div>

                            <div>
                                <label htmlFor="price" className="block text-sm font-medium text-gray-700">
                                    Price
                                </label>
                                <Input
                                    id="price"
                                    type="number"
                                    step="0.01"
                                    className="mt-1"
                                    {...register('price', { valueAsNumber: true })}
                                />
                                {errors.price && (
                                    <p className="mt-1 text-sm text-red-600">{errors.price.message}</p>
                                )}
                            </div>

                            <Button type="submit" className="w-full" size="lg" isLoading={isLoading}>
                                Create Product
                            </Button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
}

