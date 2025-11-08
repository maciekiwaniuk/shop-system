import { apiClient } from './client';
import { ApiResponse } from '@/types/api';
import { LoginCredentials, RegisterData, AuthResponse } from '@/types/auth';

export const authApi = {
    async login(credentials: LoginCredentials): Promise<ApiResponse<AuthResponse>> {
        const { data } = await apiClient.post<ApiResponse<AuthResponse>>('/login', credentials);
        return data;
    },

    async register(registerData: RegisterData): Promise<ApiResponse<AuthResponse>> {
        const { data } = await apiClient.post<ApiResponse<AuthResponse>>('/register', registerData);
        return data;
    },
};

