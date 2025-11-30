'use client';

import { createContext, useContext, useEffect, useState, ReactNode } from 'react';
import { initializeApiClients } from '@/lib/api/client';

interface RuntimeConfigContextType {
	isReady: boolean;
}

const RuntimeConfigContext = createContext<RuntimeConfigContextType>({
	isReady: false,
});

export function useRuntimeConfig() {
	return useContext(RuntimeConfigContext);
}

export function RuntimeConfigProvider({ children }: { children: ReactNode }) {
	const [isReady, setIsReady] = useState(false);

	useEffect(() => {
		const init = async () => {
			await initializeApiClients();
			setIsReady(true);
		};
		init();
	}, []);

	// Show loading state while config is being fetched
	if (!isReady) {
		return (
			<div className="flex min-h-screen items-center justify-center">
				<div className="text-center">
					<div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-blue-600 border-r-transparent"></div>
					<p className="mt-4 text-gray-600">Initializing...</p>
				</div>
			</div>
		);
	}

	return (
		<RuntimeConfigContext.Provider value={{ isReady }}>
			{children}
		</RuntimeConfigContext.Provider>
	);
}

