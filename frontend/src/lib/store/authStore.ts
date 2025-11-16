import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';

interface AuthState {
    token: string | null;
    isAuthenticated: boolean;
    hasHydrated: boolean;
    setToken: (token: string | null) => void;
    setHasHydrated: (hasHydrated: boolean) => void;
    logout: () => void;
}

const getStorage = () => {
    if (typeof window === 'undefined') {
        return {
            getItem: () => null,
            setItem: () => {},
            removeItem: () => {},
        };
    }
    return localStorage;
};

export const useAuthStore = create<AuthState>()(
    persist(
        (set) => ({
            token: null,
            isAuthenticated: false,
            hasHydrated: false,
            setToken: (token) => {
                set({
                    token,
                    isAuthenticated: !!token,
                });
                // Sync with localStorage for API client
                if (typeof window !== 'undefined') {
                    if (token) {
                        localStorage.setItem('auth_token', token);
                    } else {
                        localStorage.removeItem('auth_token');
                    }
                }
            },
            setHasHydrated: (hasHydrated: boolean) => {
                set({ hasHydrated });
            },
            logout: () => {
                set({
                    token: null,
                    isAuthenticated: false,
                });
                if (typeof window !== 'undefined') {
                    localStorage.removeItem('auth_token');
                }
            },
        }),
        {
            name: 'auth-storage',
            storage: createJSONStorage(() => getStorage()),
            partialize: (state) => ({ token: state.token, isAuthenticated: state.isAuthenticated }),
            onRehydrateStorage: () => (state) => {
                // Mark hydration finished so UI can safely gate redirects
                state?.setHasHydrated(true);
            },
        }
    )
);

