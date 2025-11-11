// Utility helpers for JWT and role checks
import { useMemo } from 'react';
import { useAuthStore } from '@/lib/store/authStore';

export type DecodedJwt = {
	// Common JWT fields
	exp?: number;
	iat?: number;
	roles?: string[]; // Symfony/LexikJWT typically includes roles
	// Allow any additional claims
	[key: string]: any;
};

export function decodeJwt(token: string | null | undefined): DecodedJwt | null {
	if (!token) return null;
	try {
		const parts = token.split('.');
		if (parts.length < 2) return null;
		const payloadB64 = parts[1].replace(/-/g, '+').replace(/_/g, '/');
		const decoded = atob(payloadB64);
		return JSON.parse(decoded);
	} catch {
		return null;
	}
}

export function isJwtExpired(token: string | null | undefined): boolean {
	const payload = decodeJwt(token);
	if (!payload?.exp) return false;
	const nowSec = Math.floor(Date.now() / 1000);
	return nowSec >= Number(payload.exp);
}

export function tokenHasRole(token: string | null | undefined, role: string): boolean {
	const payload = decodeJwt(token);
	const roles: string[] = Array.isArray(payload?.roles) ? payload!.roles : [];
	return roles.includes(role);
}

export function useIsAdmin(): boolean {
	const token = useAuthStore((s) => s.token);
	return useMemo(() => tokenHasRole(token, 'ROLE_ADMIN') && !isJwtExpired(token), [token]);
}


