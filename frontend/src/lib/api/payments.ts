import { paymentsClient } from './client';
import { ApiResponse } from '@/types/api';
import { Transaction, InitiateTransactionRequest } from '@/types/payment';

export const paymentsApi = {
    async initiateTransaction(request: InitiateTransactionRequest): Promise<ApiResponse> {
        const { data } = await paymentsClient.post<ApiResponse>('/transactions/initiate', request);
        return data;
    },

    async completeTransaction(transactionId: string): Promise<ApiResponse> {
        const { data } = await paymentsClient.put<ApiResponse>(`/transactions/complete/${transactionId}`);
        return data;
    },

    async cancelTransaction(transactionId: string): Promise<ApiResponse> {
        const { data } = await paymentsClient.put<ApiResponse>(`/transactions/cancel/${transactionId}`);
        return data;
    },

    async getTransactionById(transactionId: string): Promise<ApiResponse<Transaction>> {
        const { data } = await paymentsClient.get<ApiResponse<Transaction>>(`/transactions/by-id/${transactionId}`);
        return data;
    },

    async getTransactionsByPayerId(payerId: string): Promise<ApiResponse<Transaction[]>> {
        const { data } = await paymentsClient.get<ApiResponse<Transaction[]>>(`/transactions/by-payer-id/${payerId}`);
        return data;
    },
};

