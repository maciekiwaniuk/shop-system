export interface ApiResponse<T = unknown> {
    success: boolean;
    message?: string;
    errors?: Record<string, string>;
    data?: T;
}

export interface PaginationParams {
    offset?: number;
    limit?: number;
}

