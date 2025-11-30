import { NextResponse } from 'next/server';

export async function GET() {
	// Use non-NEXT_PUBLIC_ vars for true runtime config
	// These will be read at runtime, not baked into the bundle
	const config = {
		apiUrl: process.env.API_URL || process.env.NEXT_PUBLIC_API_URL || 'http://localhost/api/v1',
		paymentsUrl: process.env.PAYMENTS_URL || process.env.NEXT_PUBLIC_PAYMENTS_URL || '/payments',
	};
	
	console.log('[API Config Route] Serving runtime config:', config);
	console.log('[API Config Route] All environment variables:', {
		API_URL: process.env.API_URL,
		PAYMENTS_URL: process.env.PAYMENTS_URL,
		NEXT_PUBLIC_API_URL: process.env.NEXT_PUBLIC_API_URL,
		NEXT_PUBLIC_PAYMENTS_URL: process.env.NEXT_PUBLIC_PAYMENTS_URL,
	});
	
	return NextResponse.json(config);
}

export const dynamic = 'force-dynamic'; // Ensure this is not cached

