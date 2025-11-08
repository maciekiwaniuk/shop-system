import { apiClient } from './client';
import { ApiResponse } from '@/types/api';
import { Order, PaginatedOrders } from '@/types/order';

export const ordersApi = {
    async getPaginated(cursor?: string, limit: number = 10): Promise<ApiResponse<PaginatedOrders>> {
        const params: Record<string, string | number> = { limit };
        if (cursor) {
            params.cursor = cursor;
        }
        const { data } = await apiClient.get<ApiResponse<PaginatedOrders>>('/orders/get-paginated', {
            params,
        });
        return data;
    },

    async getByUuid(uuid: string): Promise<ApiResponse<Order>> {
        const { data } = await apiClient.get<ApiResponse<Order>>(`/orders/show/${uuid}`);
        return data;
    },

    async create(products: Array<{ id: number; quantity: number; pricePerPiece: number }>): Promise<ApiResponse> {
        const { data } = await apiClient.post<ApiResponse>('/orders/create', { products });
        return data;
    },

    async changeStatus(uuid: string, status: string): Promise<ApiResponse> {
        const { data } = await apiClient.post<ApiResponse>(`/orders/change-status/${uuid}`, { status });
        return data;
    },
};

