'use client';

import { useState, useEffect, Suspense } from 'react';
import { useParams, useRouter } from 'next/navigation';
import Link from 'next/link';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { productsApi } from '@/lib/api/products';
import { Product } from '@/types/product';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { LoadingSpinner } from '@/components/shared/LoadingSpinner';
import { ArrowLeft } from 'lucide-react';
import { toast } from 'react-hot-toast';

const updateProductSchema = z.object({
    name: z.string().min(1, 'Name is required'),
    slug: z.string().min(1, 'Slug is required'),
    price: z.number().positive('Price must be greater than 0'),
});

type UpdateProductFormData = z.infer<typeof updateProductSchema>;

function EditProductContent() {
    const params = useParams();
    const router = useRouter();
    const productId = parseInt(params.id as string);
    const [product, setProduct] = useState<Product | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [isSaving, setIsSaving] = useState(false);

    const {
        register,
        handleSubmit,
        formState: { errors },
        reset,
    } = useForm<UpdateProductFormData>({
        resolver: zodResolver(updateProductSchema),
    });

    useEffect(() => {
        const loadProduct = async () => {
            try {
                // We need to get product by ID, but API only has getBySlug
                // For now, we'll need to fetch from list or modify API
                // This is a placeholder - you may need to adjust based on your API
                const response = await productsApi.getPaginated({ offset: 0, limit: 1000 });
                if (response.success && response.data) {
                    const productsList = Array.isArray(response.data)
                        ? response.data
                        : response.data.products || [];
                    const foundProduct = productsList.find((p) => p.id === productId);
                    if (foundProduct) {
                        setProduct(foundProduct);
                        reset({
                            name: foundProduct.name,
                            slug: foundProduct.slug,
                            price: foundProduct.price,
                        });
                    } else {
                        toast.error('Product not found');
                        router.push('/admin/products');
                    }
                }
            } catch (error) {
                console.error('Error loading product:', error);
                toast.error('Failed to load product');
                router.push('/admin/products');
            } finally {
                setIsLoading(false);
            }
        };

        if (productId) {
            loadProduct();
        }
    }, [productId, router, reset]);

    const onSubmit = async (data: UpdateProductFormData) => {
        if (!product) return;

        setIsSaving(true);
        try {
            const response = await productsApi.update(productId, data);
            if (response.success) {
                toast.success('Product updated successfully!');
                router.push('/admin/products');
            } else {
                if (response.errors) {
                    Object.entries(response.errors).forEach(([field, message]) => {
                        toast.error(String(message));
                    });
                } else {
                    toast.error(response.message || 'Failed to update product');
                }
            }
        } catch (error: any) {
            console.error('Update product error:', error);
            
            // Handle 401/403 errors specifically
            if (error?.response?.status === 401 || error?.response?.status === 403) {
                const errorMessage = error?.response?.data?.message || 'Access denied';
                if (errorMessage.toLowerCase().includes('access denied') || 
                    errorMessage.toLowerCase().includes('forbidden') ||
                    errorMessage.toLowerCase().includes('permission') ||
                    errorMessage.toLowerCase().includes('full authentication')) {
                    toast.error('You do not have permission to update products. Admin access required.');
                } else {
                    toast.error('Authentication failed. Please log in again.');
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
                    toast.error('Failed to update product');
                }
            } else {
                toast.error('An error occurred while updating product');
            }
        } finally {
            setIsSaving(false);
        }
    };

    if (isLoading) {
        return (
            <div className="flex min-h-screen items-center justify-center">
                <LoadingSpinner size="lg" />
            </div>
        );
    }

    if (!product) {
        return null;
    }

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
                    <h1 className="mb-8 text-3xl font-bold text-gray-900">Edit Product</h1>

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

                            <Button type="submit" className="w-full" size="lg" isLoading={isSaving}>
                                Update Product
                            </Button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default function EditProductPage() {
    return (
        <Suspense fallback={
            <div className="flex min-h-screen items-center justify-center">
                <LoadingSpinner size="lg" />
            </div>
        }>
            <EditProductContent />
        </Suspense>
    );
}

