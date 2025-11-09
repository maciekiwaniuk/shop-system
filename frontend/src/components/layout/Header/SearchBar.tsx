'use client';

import { useState, useEffect, useRef } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Input } from '@/components/ui/Input';
import { Search, Loader2 } from 'lucide-react';
import { useDebounce } from '@/lib/hooks/useDebounce';
import { productsApi } from '@/lib/api/products';
import { Product } from '@/types/product';
import { formatPrice } from '@/lib/utils/format';

export function SearchBar() {
    const router = useRouter();
    const [searchTerm, setSearchTerm] = useState('');
    const [results, setResults] = useState<Product[]>([]);
    const [isSearching, setIsSearching] = useState(false);
    const [showResults, setShowResults] = useState(false);
    const searchContainerRef = useRef<HTMLDivElement>(null);
    const debouncedSearch = useDebounce(searchTerm, 300);

    // Search when debounced term changes and has 3+ characters
    useEffect(() => {
        const performSearch = async () => {
            if (debouncedSearch.trim().length >= 3) {
                setIsSearching(true);
                // Small delay to show loading state for better UX
                await new Promise((resolve) => setTimeout(resolve, 100));
                try {
                    const response = await productsApi.search(debouncedSearch.trim());
                    if (response.success && response.data) {
                        const products = Array.isArray(response.data) ? response.data : [];
                        // Limit to maximum 4 results to avoid scrollable list
                        setResults(products.slice(0, 4));
                        setShowResults(true);
                    } else {
                        setResults([]);
                        setShowResults(true);
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    setResults([]);
                    setShowResults(true);
                } finally {
                    setIsSearching(false);
                }
            } else {
                setResults([]);
                setShowResults(false);
                setIsSearching(false);
            }
        };

        performSearch();
    }, [debouncedSearch]);

    // Close dropdown when clicking outside
    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (
                searchContainerRef.current &&
                !searchContainerRef.current.contains(event.target as Node)
            ) {
                setShowResults(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, []);

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        setSearchTerm(value);
        if (value.trim().length >= 3) {
            setShowResults(true);
        } else {
            setShowResults(false);
            setResults([]);
        }
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (searchTerm.trim().length >= 3) {
            router.push(`/?search=${encodeURIComponent(searchTerm.trim())}`);
            setShowResults(false);
        }
    };


    return (
        <div ref={searchContainerRef} className="relative w-full max-w-md">
            <form onSubmit={handleSubmit} className="relative">
                <div className="relative">
                    <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <Input
                        type="text"
                        placeholder="Search products..."
                        value={searchTerm}
                        onChange={handleInputChange}
                        onFocus={() => {
                            if (results.length > 0 && searchTerm.trim().length >= 3) {
                                setShowResults(true);
                            }
                        }}
                        className="w-full pl-10 pr-10"
                    />
                    {isSearching && (
                        <Loader2 className="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 animate-spin text-gray-400" />
                    )}
                </div>
            </form>

            {/* Search Results Dropdown */}
            {showResults && (
                <div className="absolute z-50 mt-2 w-full rounded-lg border-2 border-gray-200 bg-white shadow-lg transition-all duration-200">
                    {isSearching ? (
                        <div className="flex items-center justify-center p-8">
                            <Loader2 className="h-6 w-6 animate-spin text-gray-400" />
                            <span className="ml-3 text-sm text-gray-600">Searching...</span>
                        </div>
                    ) : results.length > 0 ? (
                        <>
                            <div>
                                {results.map((product, index) => (
                                    <Link
                                        key={`${product.id ?? 'noid'}-${product.slug ?? index}`}
                                        href={`/products/${product.slug}`}
                                        onClick={() => {
                                            setShowResults(false);
                                            setSearchTerm('');
                                        }}
                                        className="flex items-center gap-4 border-b border-gray-100 p-4 transition-colors hover:bg-gray-50 last:border-0"
                                    >
                                        <div className="flex h-12 w-12 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                                            <span className="text-xl opacity-60">📦</span>
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <h4 className="truncate font-semibold text-gray-900">{product.name}</h4>
                                            <p className="text-sm text-gray-600">{formatPrice(product.price)}</p>
                                        </div>
                                    </Link>
                                ))}
                            </div>
                            <div className="border-t border-gray-200 bg-gray-50 p-2 text-center">
                                <button
                                    onClick={handleSubmit}
                                    className="text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors"
                                >
                                    View all results ({results.length})
                                </button>
                            </div>
                        </>
                    ) : debouncedSearch.trim().length >= 3 ? (
                        <div className="p-4 text-center">
                            <p className="text-sm text-gray-600">No products found</p>
                        </div>
                    ) : null}
                </div>
            )}
        </div>
    );
}

