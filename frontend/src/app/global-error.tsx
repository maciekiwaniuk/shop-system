'use client';

export default function GlobalError({
	error,
	reset,
}: {
	error: Error & { digest?: string };
	reset: () => void;
}) {
	return (
		<html lang="en">
			<body className="antialiased">
				<div className="flex min-h-screen items-center justify-center bg-gray-50 px-6">
					<div className="w-full max-w-md rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
						<h1 className="text-xl font-semibold text-gray-900">Something went wrong</h1>
						<p className="mt-2 text-sm text-gray-600">
							Please try again. If the problem persists, contact support.
						</p>

						<details className="mt-4 rounded border border-gray-100 bg-gray-50 p-3">
							<summary className="cursor-pointer text-sm font-medium text-gray-700">
								Error details
							</summary>
							<pre className="mt-2 overflow-auto text-xs text-gray-700">
								{error?.message}
								{error?.digest ? `\nDigest: ${error.digest}` : ''}
							</pre>
						</details>

						<div className="mt-6 flex gap-3">
							<button
								type="button"
								onClick={() => reset()}
								className="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
							>
								Try again
							</button>
							<button
								type="button"
								onClick={() => (window.location.href = '/')}
								className="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-50"
							>
								Go home
							</button>
						</div>
					</div>
				</div>
			</body>
		</html>
	);
}

