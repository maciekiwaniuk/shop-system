export interface LoginCredentials {
    email: string;
    password: string;
}

export interface RegisterData {
    email: string;
    password: string;
    name: string;
    surname?: string;
}

export interface AuthResponse {
    token: string;
}

export interface User {
    id: string;
    email: string;
    name?: string;
    surname?: string;
}

