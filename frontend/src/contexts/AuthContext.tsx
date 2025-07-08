'use client';

import React, { createContext, useContext, useEffect, useState, ReactNode } from 'react';
import { authService, User, LoginRequest, RegisterRequest, AuthResponse } from '@/lib/auth';

interface AuthContextType {
    user: User | null;
    isAuthenticated: boolean;
    isAdmin: boolean;
    isLoading: boolean;
    login: (credentials: LoginRequest) => Promise<AuthResponse>;
    register: (userData: RegisterRequest) => Promise<AuthResponse>;
    logout: () => void;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function useAuth() {
    const context = useContext(AuthContext);
    if (context === undefined) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
}

interface AuthProviderProps {
    children: ReactNode;
}

export function AuthProvider({ children }: AuthProviderProps) {
    const [user, setUser] = useState<User | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        // Initialize auth state from localStorage
        const initializeAuth = () => {
            const currentUser = authService.getUser();
            setUser(currentUser);
            setIsLoading(false);
        };

        initializeAuth();
    }, []);

    const login = async (credentials: LoginRequest): Promise<AuthResponse> => {
        try {
            const response = await authService.login(credentials);
            if (response.success) {
                setUser(authService.getUser());
            }
            return response;
        } catch (error) {
            console.error('Login error in context:', error);
            throw error;
        }
    };

    const register = async (userData: RegisterRequest): Promise<AuthResponse> => {
        try {
            const response = await authService.register(userData);
            if (response.success) {
                setUser(authService.getUser());
            }
            return response;
        } catch (error) {
            console.error('Register error in context:', error);
            throw error;
        }
    };

    const logout = () => {
        authService.logout();
        setUser(null);
    };

    const value: AuthContextType = {
        user,
        isAuthenticated: !!user,
        isAdmin: user?.roles.includes('ROLE_ADMIN') || false,
        isLoading,
        login,
        register,
        logout,
    };

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
} 