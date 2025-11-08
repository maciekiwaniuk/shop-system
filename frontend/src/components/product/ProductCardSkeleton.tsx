export function ProductCardSkeleton() {
    return (
        <div className="flex flex-col overflow-hidden rounded-xl bg-white shadow-sm">
            {/* Image Skeleton */}
            <div className="aspect-square w-full animate-pulse bg-gradient-to-br from-gray-100 to-gray-200" />

            {/* Content Skeleton */}
            <div className="flex flex-1 flex-col p-5">
                <div className="mb-2 h-5 w-3/4 animate-pulse rounded bg-gray-200" />
                <div className="mb-4 h-4 w-1/2 animate-pulse rounded bg-gray-200" />
                <div className="h-11 w-full animate-pulse rounded-lg bg-gray-200" />
            </div>
        </div>
    );
}

