export type TransactionStatus = 'waiting_for_payment' | 'paid' | 'canceled';

export interface Transaction {
    id: string;
    payer_id: string;
    amount: number;
    status: TransactionStatus;
    finished_at?: string;
    created_at: string;
}

export interface InitiateTransactionRequest {
    id: string;
    payer_id: string;
    amount: number;
}

