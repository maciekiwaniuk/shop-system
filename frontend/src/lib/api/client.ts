import axios, { AxiosInstance, AxiosError, InternalAxiosRequestConfig } from 'axios';
import { ApiResponse } from '@/types/api';

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost/api/v1';
const PAYMENTS_API_BASE_URL = process.env.NEXT_PUBLIC_PAYMENTS_URL || '/payments';

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

// Public endpoints that must never send Authorization
const PUBLIC_PATHS = [
    '/products/get-paginated',
    '/products/show',
    '/products/search',
    '/health',
    '/login',
    '/register',
];

function isPublicPath(url?: string): boolean {
    if (!url) return false;
    try {
        // If a full URL is passed, extract pathname; otherwise treat as path
        const pathname = url.startsWith('http') ? new URL(url).pathname : url;
        return PUBLIC_PATHS.some((p) => pathname.startsWith(p));
    } catch {
        return PUBLIC_PATHS.some((p) => (url || '').startsWith(p));
    }
}

function isJwtExpired(token: string): boolean {
    try {
        const [, payloadB64] = token.split('.');
        if (!payloadB64) return false;
        const json = JSON.parse(atob(payloadB64.replace(/-/g, '+').replace(/_/g, '/')));
        if (!json.exp) return false;
        const nowSec = Math.floor(Date.now() / 1000);
        return nowSec >= Number(json.exp);
    } catch {
        return false;
    }
}

// Request interceptor to add JWT token (skip for public endpoints)
apiClient.interceptors.request.use(
    (config: InternalAxiosRequestConfig) => {
        if (typeof window !== 'undefined') {
            // Do not attach Authorization for public endpoints
            if (isPublicPath(config.url)) {
                return config;
            }
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
                const trimmed = token.trim();
                if (!isJwtExpired(trimmed)) {
                    config.headers.Authorization = `Bearer ${trimmed}`;
                }
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
            // Clear invalid/expired token from storage
            if (typeof window !== 'undefined') {
                console.warn('401 Unauthorized - clearing invalid token:', {
                    url: error.config?.url,
                    message: error.response?.data?.message,
                });
                localStorage.removeItem('auth_token');
                // Also clear Zustand auth storage
                try {
                    const authStorage = localStorage.getItem('auth-storage');
                    if (authStorage) {
                        const parsed = JSON.parse(authStorage);
                        parsed.state.token = null;
                        parsed.state.isAuthenticated = false;
                        localStorage.setItem('auth-storage', JSON.stringify(parsed));
                    }
                } catch (e) {
                    // If parsing fails, just remove it
                    localStorage.removeItem('auth-storage');
                }
            }
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

