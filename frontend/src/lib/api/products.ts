import { apiClient } from './client';
import { ApiResponse, PaginationParams } from '@/types/api';
import { Product, PaginatedProducts } from '@/types/product';

export const productsApi = {
    async getPaginated(params: PaginationParams): Promise<ApiResponse<PaginatedProducts>> {
        const { data } = await apiClient.get<ApiResponse<PaginatedProducts>>('/products/get-paginated', {
            params: {
                offset: params.offset || 1,
                limit: params.limit || 12,
            },
        });
        return data;
    },

    async getBySlug(slug: string): Promise<ApiResponse<Product>> {
        const { data } = await apiClient.get<ApiResponse<Product>>(`/products/show/${slug}`);
        return data;
    },

    async search(phrase: string): Promise<ApiResponse<Product[]>> {
        const { data } = await apiClient.get<ApiResponse<Product[]>>('/products/search', {
            params: { phrase },
        });
        return data;
    },

    async create(productData: { name: string; price: number }): Promise<ApiResponse<{ productId: number }>> {
        const { data } = await apiClient.post<ApiResponse<{ productId: number }>>('/products/create', productData);
        return data;
    },

    async update(id: number, productData: { name?: string; price?: number }): Promise<ApiResponse> {
        const { data } = await apiClient.put<ApiResponse>(`/products/update/${id}`, productData);
        return data;
    },

    async delete(id: number): Promise<ApiResponse> {
        const { data } = await apiClient.delete<ApiResponse>(`/products/delete/${id}`);
        return data;
    },
};

