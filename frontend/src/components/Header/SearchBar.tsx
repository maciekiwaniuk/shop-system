'use client'

import React, { useState, useEffect, useRef, useCallback } from 'react';
import { useRouter } from 'next/navigation';

interface ApiResponse {
    success: boolean;
    data: Product[];
}

interface Product {
    slug: string;
    name: string;
    price: number;
}

const SkeletonItem: React.FC = () => (
    <div className="p-3 h-[52px]">
        <div className="h-4 bg-gray-200 rounded w-3/4 animate-pulse"></div>
        <div className="h-3 bg-gray-200 rounded w-1/2 animate-pulse mt-1.5"></div>
    </div>
);

export default function SearchBar() {
    const [searchTerm, setSearchTerm] = useState<string>('');
    const [results, setResults] = useState<Product[]>([]);
    const [isLoading, setIsLoading] = useState<boolean>(false);
    const [showDropdown, setShowDropdown] = useState<boolean>(false);
    const [apiError, setApiError] = useState<string | null>(null);

    const searchContainerRef = useRef<HTMLDivElement>(null);
    const debounceTimeoutRef = useRef<NodeJS.Timeout | null>(null);
    const router = useRouter();

    const performSearch = useCallback(async (term: string) => {
        setApiError(null);

        try {
            const response = await fetch(`http://localhost/api/v1/products/search?phrase=${encodeURIComponent(term)}`);
            const responseData: ApiResponse = await response.json();

            if (!response.ok) {
                setApiError(`Server error, please try later...`);
                setResults([]);
                return;
            }

            if (!responseData.success) {
                setApiError('Search error');
                setResults([]);
                return;
            }

            setResults(responseData.data.slice(0, 6));
        } catch (error) {
            setApiError('Could not connect with server, please check internet connection');
            setResults([]);
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        if (debounceTimeoutRef.current) {
            clearTimeout(debounceTimeoutRef.current);
        }

        if (searchTerm.length < 3) {
            setResults([]);
            setShowDropdown(false);
            setIsLoading(false);
            setApiError(null);
            return;
        }

        setShowDropdown(true);
        setIsLoading(true);
        setApiError(null);

        debounceTimeoutRef.current = setTimeout(() => {
            performSearch(searchTerm);
        }, 300);

        return () => {
            if (debounceTimeoutRef.current) {
                clearTimeout(debounceTimeoutRef.current);
            }
        };
    }, [searchTerm, performSearch]);

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (searchContainerRef.current && !searchContainerRef.current.contains(event.target as Node)) {
                setShowDropdown(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, []);

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setSearchTerm(e.target.value);
    };

    const handleInputFocus = () => {
        if (searchTerm.length >= 3 || apiError) {
            setShowDropdown(true);
        }
    };

    const handleResultClick = (product: Product) => {
        setSearchTerm(product.name);
        setShowDropdown(false);
        setApiError(null);
    };

    return (
        <div className="relative w-full max-w-lg mx-auto" ref={searchContainerRef}>
            <div
                className={`bg-gray-100 flex items-center border max-md:order-1 px-4 rounded-sm h-10 w-full transition-all ${
                    apiError ? 'border-red-500 focus-within:ring-red-300' : 'border-transparent focus-within:border-black focus-within:ring-black'
                } focus-within:ring-1 focus-within:bg-transparent`}
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192.904 192.904"
                     className="fill-gray-400 mr-4 w-4 h-4 shrink-0">
                    <path
                        d="m190.707 180.101-47.078-47.077c11.702-14.072 18.752-32.142 18.752-51.831C162.381 36.423 125.959 0 81.191 0 36.422 0 0 36.423 0 81.193c0 44.767 36.422 81.187 81.191 81.187 19.688 0 37.759-7.049 51.831-18.751l47.079 47.078a7.474 7.474 0 0 0 5.303 2.197 7.498 7.498 0 0 0 5.303-12.803zM15 81.193C15 44.694 44.693 15 81.191 15c36.497 0 66.189 29.694 66.189 66.193 0 36.496-29.692 66.187-66.189 66.187C44.693 147.38 15 117.689 15 81.193z">
                    </path>
                </svg>
                <input type='text'
                       placeholder='Search for products...'
                       className="w-full outline-none bg-transparent text-slate-900 text-sm"
                       value={searchTerm}
                       onChange={handleInputChange}
                       onFocus={handleInputFocus}
                       aria-label="Search for products"
                />
            </div>

            {showDropdown && (
                <div className="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg z-20 max-h-96 overflow-y-auto">
                    {isLoading && (
                        <>
                            <SkeletonItem />
                            <SkeletonItem />
                            <SkeletonItem />
                        </>
                    )}
                    {!isLoading && apiError && (
                        <div className="px-4 py-3 text-sm text-red-600 bg-red-50">
                            {apiError}
                        </div>
                    )}
                    {!isLoading && !apiError && results.length > 0 && (
                        <ul>
                            {results.map((product) => (
                                <li
                                    key={product.slug}
                                    className="px-4 py-3 hover:bg-gray-100 cursor-pointer text-sm text-gray-800 border-b border-gray-200 last:border-b-0"
                                    onClick={() => handleResultClick(product)}
                                    role="option"
                                    aria-selected={false}
                                >
                                    {product.name} - <span className="text-xs text-gray-500">{product.price.toFixed(2)} $</span>
                                </li>
                            ))}
                        </ul>
                    )}
                    {!isLoading && !apiError && results.length === 0 && searchTerm.length >= 3 && (
                        <div className="px-4 py-3 text-sm text-gray-500">
                            No results for "{searchTerm}"
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}