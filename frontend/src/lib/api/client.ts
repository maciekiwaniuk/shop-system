import axios, { AxiosInstance, AxiosError, InternalAxiosRequestConfig } from 'axios';
import { ApiResponse } from '@/types/api';

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost/api/v1';
const PAYMENTS_API_BASE_URL = process.env.NEXT_PUBLIC_PAYMENTS_API_URL || 'http://localhost:8080/v1';

// Create axios instance for main API
export const apiClient: AxiosInstance = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
    },
});

// Create axios instance for payments service
export const paymentsClient: AxiosInstance = axios.create({
    baseURL: PAYMENTS_API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
    },
});

// Request interceptor to add JWT token
apiClient.interceptors.request.use(
    (config: InternalAxiosRequestConfig) => {
        if (typeof window !== 'undefined') {
            // Try to get token from localStorage
            let token = localStorage.getItem('auth_token');
            
            // Also check auth-storage (Zustand persist storage)
            if (!token) {
                try {
                    const authStorage = localStorage.getItem('auth-storage');
                    if (authStorage) {
                        const parsed = JSON.parse(authStorage);
                        token = parsed?.state?.token || null;
                    }
                } catch (e) {
                    // Ignore parse errors
                }
            }
            
            if (token && config.headers) {
                config.headers.Authorization = `Bearer ${token}`;
            } else if (!token && config.url && !config.url.includes('/login') && !config.url.includes('/register')) {
                // Log warning if token is missing for protected routes
                console.warn('No auth token found for request:', config.url);
            }
        }
        return config;
    },
    (error: AxiosError) => {
        return Promise.reject(error);
    }
);

// Response interceptor to handle errors
apiClient.interceptors.response.use(
    (response) => response,
    (error: AxiosError<ApiResponse>) => {
        if (error.response?.status === 401) {
            // Don't auto-logout immediately - let components handle the error
            // Components will show appropriate error messages
            console.warn('401 Unauthorized response:', {
                url: error.config?.url,
                message: error.response?.data?.message,
                hasToken: typeof window !== 'undefined' && !!localStorage.getItem('auth_token'),
            });
        }
        return Promise.reject(error);
    }
);

paymentsClient.interceptors.response.use(
    (response) => response,
    (error: AxiosError<ApiResponse>) => {
        return Promise.reject(error);
    }
);

