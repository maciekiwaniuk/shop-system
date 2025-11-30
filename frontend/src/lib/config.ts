export interface RuntimeConfig {
	apiUrl: string;
	paymentsUrl: string;
}

let runtimeConfig: RuntimeConfig | null = null;

export async function getRuntimeConfig(): Promise<RuntimeConfig> {
	if (runtimeConfig) {
		console.log('[Config] Using cached config:', runtimeConfig);
		return runtimeConfig;
	}

	try {
		console.log('[Config] Fetching from /api/config...');
		const response = await fetch('/api/config');
		const config = (await response.json()) as RuntimeConfig;
		runtimeConfig = config;
		console.log('[Config] Loaded successfully:', config);
		return config;
	} catch (error) {
		console.error('[Config] Failed to load runtime config:', error);
		// Fallback to defaults
		const fallbackConfig: RuntimeConfig = {
			apiUrl: 'http://localhost/api/v1',
			paymentsUrl: '/payments',
		};
		console.warn('[Config] Using fallback config:', fallbackConfig);
		return fallbackConfig;
	}
}

