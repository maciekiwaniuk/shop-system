import type { NextConfig } from 'next';

const nextConfig: NextConfig = {
	output: 'standalone',
	async rewrites() {
		return [
			{
				source: '/payments/:path*',
				destination: 'http://shop-system-payments:8080/api/v1/:path*',
			},
		];
	},
};

export default nextConfig;
