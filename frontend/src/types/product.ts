export interface Product {
    id: number;
    name: string;
    slug: string;
    price: number;
    createdAt: string;
    updatedAt: string;
}

export interface PaginatedProducts {
    products: Product[];
    total: number;
    offset: number;
    limit: number;
}

