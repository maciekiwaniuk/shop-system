import { Product } from './product';

// Backend statuses: waiting_for_payment | canceled | completed
export type BackendOrderStatus = 'waiting_for_payment' | 'canceled' | 'completed';

export interface OrderProduct {
	id: number;
	product: Product;
	productQuantity: number;
	productPricePerPiece: number;
}

export interface OrderStatusUpdate {
	id: string;
	status: BackendOrderStatus;
	createdAt: string;
}

export interface Order {
	id: string;
	createdAt: string;
	completedAt?: string | null;
	ordersProducts?: OrderProduct[];
	ordersStatusUpdates?: OrderStatusUpdate[];
}

export interface PaginatedOrders {
	orders: Order[];
	cursor?: string;
	limit: number;
}

