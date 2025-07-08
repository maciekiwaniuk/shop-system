export interface User {
    id: number;
    email: string;
    roles: string[];
}

export interface AuthResponse {
    success: boolean;
    message: string;
    data: {
        token: string;
    };
}

export interface LoginRequest {
    email: string;
    password: string;
}

export interface RegisterRequest {
    email: string;
    name: string;
    surname: string;
    password: string;
}

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost/api/v1';

class AuthService {
    private static instance: AuthService;
    private token: string | null = null;
    private user: User | null = null;

    private constructor() {
        // Load token from localStorage on initialization
        if (typeof window !== 'undefined') {
            this.token = localStorage.getItem('auth_token');
            const userStr = localStorage.getItem('auth_user');
            if (userStr) {
                try {
                    this.user = JSON.parse(userStr);
                } catch (e) {
                    console.error('Failed to parse user from localStorage:', e);
                }
            }
        }
    }

    public static getInstance(): AuthService {
        if (!AuthService.instance) {
            AuthService.instance = new AuthService();
        }
        return AuthService.instance;
    }

    public getToken(): string | null {
        return this.token;
    }

    public getUser(): User | null {
        return this.user;
    }

    public isAuthenticated(): boolean {
        return !!this.token;
    }

    public isAdmin(): boolean {
        return this.user?.roles.includes('ROLE_ADMIN') || false;
    }

    public async login(credentials: LoginRequest): Promise<AuthResponse> {
        try {
            const response = await fetch(`${API_BASE_URL}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(credentials),
            });

            const data = await response.json();

            if (response.ok && data.success && data.data?.token) {
                this.token = data.data.token;
                this.user = this.decodeToken(data.data.token);
                
                // Store in localStorage
                if (typeof window !== 'undefined') {
                    localStorage.setItem('auth_token', this.token);
                    localStorage.setItem('auth_user', JSON.stringify(this.user));
                }
                
                return {
                    success: true,
                    message: data.message || 'Login successful',
                    data: data.data
                };
            } else {
                // Handle error response
                return {
                    success: false,
                    message: data.message || data.error || 'Login failed',
                    data: { token: '' }
                };
            }
        } catch (error) {
            console.error('Login error:', error);
            return {
                success: false,
                message: 'An error occurred during login',
                data: { token: '' }
            };
        }
    }

    public async register(userData: RegisterRequest): Promise<AuthResponse> {
        try {
            const response = await fetch(`${API_BASE_URL}/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData),
            });

            const data = await response.json();

            if (response.ok && data.success && data.data?.token) {
                this.token = data.data.token;
                this.user = this.decodeToken(data.data.token);
                
                // Store in localStorage
                if (typeof window !== 'undefined') {
                    localStorage.setItem('auth_token', this.token);
                    localStorage.setItem('auth_user', JSON.stringify(this.user));
                }
                
                return {
                    success: true,
                    message: data.message || 'Registration successful',
                    data: data.data
                };
            } else {
                // Handle error response
                return {
                    success: false,
                    message: data.message || data.error || 'Registration failed',
                    data: { token: '' }
                };
            }
        } catch (error) {
            console.error('Register error:', error);
            return {
                success: false,
                message: 'An error occurred during registration',
                data: { token: '' }
            };
        }
    }

    public logout(): void {
        this.token = null;
        this.user = null;
        
        if (typeof window !== 'undefined') {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');
        }
    }

    public async makeAuthenticatedRequest(url: string, options: RequestInit = {}): Promise<Response> {
        if (!this.token) {
            throw new Error('No authentication token available');
        }

        const response = await fetch(url, {
            ...options,
            headers: {
                ...options.headers,
                'Authorization': `Bearer ${this.token}`,
                'Content-Type': 'application/json',
            },
        });

        // If token is expired, logout user
        if (response.status === 401) {
            this.logout();
            throw new Error('Authentication expired');
        }

        return response;
    }

    private decodeToken(token: string): User {
        try {
            const base64Url = token.split('.')[1];
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));

            const payload = JSON.parse(jsonPayload);
            
            return {
                id: payload.id || payload.sub,
                email: payload.email,
                roles: payload.roles || [],
            };
        } catch (error) {
            console.error('Failed to decode token:', error);
            throw new Error('Invalid token');
        }
    }
}

export const authService = AuthService.getInstance(); 