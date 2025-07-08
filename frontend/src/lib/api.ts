import { authService } from './auth';

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost/api/v1';

export interface ApiResponse<T = any> {
    success: boolean;
    message?: string;
    data?: T;
    errors?: Record<string, string[]>;
}

class ApiService {
    private async makeRequest<T>(
        endpoint: string,
        options: RequestInit = {}
    ): Promise<ApiResponse<T>> {
        try {
            const url = `${API_BASE_URL}${endpoint}`;
            const response = await authService.makeAuthenticatedRequest(url, options);
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                return {
                    success: false,
                    message: errorData.message || `HTTP ${response.status}`,
                    errors: errorData.errors,
                };
            }

            const data = await response.json();
            return {
                success: true,
                ...data,
            };
        } catch (error) {
            console.error('API request error:', error);
            return {
                success: false,
                message: error instanceof Error ? error.message : 'An error occurred',
            };
        }
    }

    // Products API
    async getProducts(offset: number = 0, limit: number = 10) {
        return this.makeRequest(`/products/get-paginated?offset=${offset}&limit=${limit}`);
    }

    async getProduct(slug: string) {
        return this.makeRequest(`/products/show/${slug}`);
    }

    async searchProducts(phrase: string) {
        return this.makeRequest(`/products/search?phrase=${encodeURIComponent(phrase)}`);
    }

    async createProduct(productData: any) {
        return this.makeRequest('/products/create', {
            method: 'POST',
            body: JSON.stringify(productData),
        });
    }

    async updateProduct(id: number, productData: any) {
        return this.makeRequest(`/products/update/${id}`, {
            method: 'PUT',
            body: JSON.stringify(productData),
        });
    }

    async deleteProduct(id: number) {
        return this.makeRequest(`/products/delete/${id}`, {
            method: 'DELETE',
        });
    }

    // Orders API
    async getOrders(cursor?: string, limit: number = 10) {
        const params = new URLSearchParams({ limit: limit.toString() });
        if (cursor) params.append('cursor', cursor);
        
        return this.makeRequest(`/orders/get-paginated?${params.toString()}`);
    }

    async getOrder(uuid: string) {
        return this.makeRequest(`/orders/show/${uuid}`);
    }

    async createOrder(orderData: any) {
        return this.makeRequest('/orders/create', {
            method: 'POST',
            body: JSON.stringify(orderData),
        });
    }

    async changeOrderStatus(uuid: string, status: string) {
        return this.makeRequest(`/orders/change-status/${uuid}`, {
            method: 'POST',
            body: JSON.stringify({ status }),
        });
    }
}

export const apiService = new ApiService(); 