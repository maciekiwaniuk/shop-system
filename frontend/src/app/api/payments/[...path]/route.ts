import { NextRequest } from 'next/server';

const INTERNAL_PAYMENTS_BASE =
	process.env.PAYMENTS_INTERNAL_URL ||
	'http://shop-system-payments:8080/api/v1';

async function proxy(request: NextRequest, path: string[]) {
	const targetUrl =
		INTERNAL_PAYMENTS_BASE.replace(/\/+$/, '') +
		'/' +
		path.map(encodeURIComponent).join('/') +
		(request.nextUrl.search || '');

	const method = request.method;
	const headers = new Headers();
	// Forward only safe headers
	const auth = request.headers.get('authorization');
	if (auth) headers.set('authorization', auth);
	headers.set('content-type', request.headers.get('content-type') || 'application/json');

	const init: RequestInit = {
		method,
		headers,
		// GET/HEAD must not have body
		body: method === 'GET' || method === 'HEAD' ? undefined : await request.text(),
	};

	const resp = await fetch(targetUrl, init);
	const text = await resp.text();

	// Return upstream response as-is
	return new Response(text, {
		status: resp.status,
		headers: {
			'content-type': resp.headers.get('content-type') || 'application/json',
		},
	});
}

export async function GET(request: NextRequest, context: { params: { path: string[] } }) {
	return proxy(request, context.params.path || []);
}

export async function POST(request: NextRequest, context: { params: { path: string[] } }) {
	return proxy(request, context.params.path || []);
}

export async function PUT(request: NextRequest, context: { params: { path: string[] } }) {
	return proxy(request, context.params.path || []);
}

export async function OPTIONS() {
	// Allow CORS for external callers if needed (not required for same-origin)
	return new Response(null, {
		status: 204,
		headers: {
			'Access-Control-Allow-Origin': '*',
			'Access-Control-Allow-Methods': 'GET, POST, OPTIONS',
			'Access-Control-Allow-Headers': 'Content-Type, Authorization',
		},
	});
}


