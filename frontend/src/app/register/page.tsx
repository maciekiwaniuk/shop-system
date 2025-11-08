'use client';

import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { useState, useEffect } from 'react';
import { useAuthStore } from '@/lib/store/authStore';
import { authApi } from '@/lib/api/auth';
import { registerSchema, type RegisterFormData } from '@/lib/utils/validation';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { toast } from 'react-hot-toast';

export default function RegisterPage() {
    const router = useRouter();
    const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
    const setToken = useAuthStore((state) => state.setToken);
    const [isLoading, setIsLoading] = useState(false);

    // Redirect if already logged in
    useEffect(() => {
        if (isAuthenticated) {
            router.push('/');
        }
    }, [isAuthenticated, router]);

    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm<RegisterFormData>({
        resolver: zodResolver(registerSchema),
    });

    // Don't render form if already authenticated (will redirect)
    if (isAuthenticated) {
        return null;
    }

    const onSubmit = async (data: RegisterFormData) => {
        setIsLoading(true);
        try {
            const response = await authApi.register(data);
            if (response.success && response.data?.token) {
                setToken(response.data.token);
                toast.success('Account created successfully!');
                router.push('/');
                router.refresh();
            } else {
                // Handle validation errors from backend
                if (response.errors) {
                    // Combine all errors into a single message
                    const errorMessages = Object.values(response.errors).join(', ');
                    toast.error(errorMessages, {
                        id: 'register-error', // Use same ID to prevent duplicates
                    });
                } else {
                    toast.error(response.message || 'Failed to create account', {
                        id: 'register-error',
                    });
                }
            }
        } catch (error: any) {
            console.error('Registration error:', error);
            
            // Extract error response from axios error
            if (error?.response?.data) {
                const errorData = error.response.data;
                
                // Handle validation errors
                if (errorData.errors) {
                    // Combine all errors into a single message
                    const errorMessages = Object.values(errorData.errors).join(', ');
                    toast.error(errorMessages, {
                        id: 'register-error', // Use same ID to prevent duplicates
                    });
                } else if (errorData.message) {
                    toast.error(errorData.message, {
                        id: 'register-error',
                    });
                } else {
                    toast.error('Failed to create account', {
                        id: 'register-error',
                    });
                }
            } else {
                toast.error('An error occurred while creating your account', {
                    id: 'register-error',
                });
            }
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div className="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
            <div className="w-full max-w-md space-y-8">
                <div>
                    <h2 className="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                        Create your account
                    </h2>
                    <p className="mt-2 text-center text-sm text-gray-600">
                        Already have an account?{' '}
                        <Link
                            href="/login"
                            className="font-medium text-gray-900 hover:text-gray-700"
                        >
                            Sign in
                        </Link>
                    </p>
                </div>
                <form className="mt-8 space-y-6" onSubmit={handleSubmit(onSubmit)}>
                    <div className="space-y-4 rounded-md bg-white p-8 shadow-sm">
                        <div>
                            <label htmlFor="email" className="block text-sm font-medium text-gray-700">
                                Email address
                            </label>
                            <Input
                                id="email"
                                type="email"
                                autoComplete="email"
                                className="mt-1"
                                {...register('email')}
                            />
                            {errors.email && (
                                <p className="mt-1 text-sm text-red-600">{errors.email.message}</p>
                            )}
                        </div>

                        <div>
                            <label htmlFor="name" className="block text-sm font-medium text-gray-700">
                                Name
                            </label>
                            <Input
                                id="name"
                                type="text"
                                autoComplete="given-name"
                                className="mt-1"
                                {...register('name')}
                            />
                            {errors.name && (
                                <p className="mt-1 text-sm text-red-600">{errors.name.message}</p>
                            )}
                        </div>

                        <div>
                            <label htmlFor="surname" className="block text-sm font-medium text-gray-700">
                                Surname (optional)
                            </label>
                            <Input
                                id="surname"
                                type="text"
                                autoComplete="family-name"
                                className="mt-1"
                                {...register('surname')}
                            />
                            {errors.surname && (
                                <p className="mt-1 text-sm text-red-600">{errors.surname.message}</p>
                            )}
                        </div>

                        <div>
                            <label htmlFor="password" className="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            <Input
                                id="password"
                                type="password"
                                autoComplete="new-password"
                                className="mt-1"
                                {...register('password')}
                            />
                            {errors.password && (
                                <p className="mt-1 text-sm text-red-600">{errors.password.message}</p>
                            )}
                            <p className="mt-1 text-xs text-gray-500">
                                Must be at least 8 characters long
                            </p>
                        </div>
                    </div>

                    <div>
                        <Button type="submit" className="w-full" isLoading={isLoading}>
                            Create account
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    );
}

