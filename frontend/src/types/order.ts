import { Product } from './product';

export type OrderStatus = 'pending' | 'paid' | 'shipped' | 'delivered' | 'cancelled';

export interface OrderProduct {
    id: number;
    product: Product;
    quantity: number;
    pricePerPiece: number;
}

export interface Order {
    id: string;
    uuid: string;
    status: OrderStatus;
    totalPrice: number;
    createdAt: string;
    updatedAt: string;
    ordersProducts?: OrderProduct[];
}

export interface PaginatedOrders {
    orders: Order[];
    cursor?: string;
    limit: number;
}

