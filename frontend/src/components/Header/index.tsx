'use client';

import { useState } from 'react'
import { Dialog, DialogPanel } from '@headlessui/react'
import { Bars3Icon, XMarkIcon } from '@heroicons/react/24/outline'
import Link from 'next/link'
import SearchBar from '@/components/Header/SearchBar'

export default function Header() {
    const [isOpen, setIsOpen] = useState(false)
    const [isProfileOpen, setIsProfileOpen] = useState(false)

    const toggleMenu = () => {
        setIsOpen(!isOpen)
    }

    const toggleProfile = () => {
        setIsProfileOpen(!isProfileOpen)
    }

    const navigation = [
        { name: 'Home', href: '/' },
        { name: 'Log in', href: '/login' },
        { name: 'Register', href: '/register' },
    ]

    return (
        <header className="flex shadow-md sm:px-10 px-6 py-3 bg-white min-h-[70px]">
            <div className="flex w-full max-w-screen-xl mx-auto">
                <div className="flex flex-wrap items-center justify-between relative lg:gap-y-4 gap-y-4 gap-x-4 w-full">
                    <div className="flex items-center">
                        <Link href="/">Shop system</Link>
                    </div>

                    <SearchBar />

                    <div className="flex items-center space-x-4 max-md:ml-auto">
                        <span className="relative pr-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20px"
                                height="20px"
                                className="cursor-pointer fill-gray-800 hover:fill-blue-700 inline-block"
                                viewBox="0 0 512 512"
                            >
                                <path
                                    d="M164.96 300.004h.024c.02 0 .04-.004.059-.004H437a15.003 15.003 0 0 0 14.422-10.879l60-210a15.003 15.003 0 0 0-2.445-13.152A15.006 15.006 0 0 0 497 60H130.367l-10.722-48.254A15.003 15.003 0 0 0 105 0H15C6.715 0 0 6.715 0 15s6.715 15 15 15h77.969c1.898 8.55 51.312 230.918 54.156 243.71C131.184 280.64 120 296.536 120 315c0 24.812 20.188 45 45 45h272c8.285 0 15-6.715 15-15s-6.715-15-15-15H165c-8.27 0-15-6.73-15-15 0-8.258 6.707-14.977 14.96-14.996zM477.114 90l-51.43 180H177.032l-40-180zM150 405c0 24.813 20.188 45 45 45s45-20.188 45-45-20.188-45-45-45-45 20.188-45 45zm45-15c8.27 0 15 6.73 15 15s-6.73 15-15 15-15-6.73-15-15 6.73-15 15-15zm167 15c0 24.813 20.188 45 45 45s45-20.188 45-45-20.188-45-45-45-45 20.188-45 45zm45-15c8.27 0 15 6.73 15 15s-6.73 15-15 15-15-6.73-15-15 6.73-15 15-15zm0 0"
                                    data-original="#000000"
                                />
                            </svg>
                            <span className="absolute left-auto -ml-1 top-0 rounded-full bg-blue-600 px-1 py-0 text-xs min-w-[15px] text-center text-white">
                                4
                            </span>
                        </span>

                        <div className="flex ml-auto">
                            <Link
                                href="/login"
                                className="hidden lg:block px-4 py-2 text-[15px] rounded-md font-medium text-white bg-blue-600 hover:bg-blue-700 cursor-pointer"
                            >
                                Log in
                            </Link>

                            <button
                                onClick={toggleMenu}
                                className="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 lg:hidden"
                                aria-label="Open main menu"
                            >
                                <Bars3Icon aria-hidden="true" className="size-6" />
                            </button>
                        </div>

                        <div className="relative px-1">
                            <button
                                onClick={toggleProfile}
                                className="cursor-pointer hover:fill-black"
                                aria-label="Toggle profile menu"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24px"
                                    height="24px"
                                    className="cursor-pointer hover:fill-black"
                                    viewBox="0 0 512 512"
                                >
                                    <path
                                        d="M437.02 74.981C388.667 26.629 324.38 0 256 0S123.333 26.629 74.98 74.981C26.629 123.333 0 187.62 0 256s26.629 132.667 74.98 181.019C123.333 485.371 187.62 512 256 512s132.667-26.629 181.02-74.981C485.371 388.667 512 324.38 512 256s-26.629-132.667-74.98-181.019zM256 482c-66.869 0-127.037-29.202-168.452-75.511C113.223 338.422 178.948 290 256 290c-49.706 0-90-40.294-90-90s40.294-90 90-90 90 40.294 90 90-40.294 90-90 90c77.052 0 142.777 48.422 168.452 116.489C383.037 452.798 322.869 482 256 482z"
                                        data-original="#000000"
                                    />
                                </svg>
                            </button>
                            {isProfileOpen && (
                                <div className="bg-white block z-20 shadow-lg py-6 px-6 rounded-sm sm:min-w-[320px] max-sm:min-w-[250px] absolute right-0 top-10">
                                    <ul className="space-y-1.5">
                                        <li>
                                            <Link
                                                href="/orders"
                                                className="text-sm text-gray-500 hover:text-slate-900"
                                                onClick={() => setIsProfileOpen(false)}
                                            >
                                                Orders
                                            </Link>
                                        </li>
                                    </ul>
                                    <hr className="border-b-0 my-4 border-gray-300" />
                                    <ul className="space-y-1.5">
                                        <li>
                                            <Link
                                                href="/"
                                                className="text-sm text-gray-500 hover:text-slate-900"
                                                onClick={() => setIsProfileOpen(false)}
                                            >
                                                Logout
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <Dialog open={isOpen} onClose={toggleMenu} className="lg:hidden">
                    <div
                        className="fixed inset-0 z-0 backdrop-blur-sm bg-black/20"
                        onClick={toggleMenu}
                        aria-hidden="true"
                    />
                    <DialogPanel className="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
                        <div className="flex items-center justify-end">
                            <button
                                onClick={toggleMenu}
                                className="-m-2.5 rounded-md p-2.5 text-gray-700"
                                aria-label="Close menu"
                            >
                                <XMarkIcon aria-hidden="true" className="size-6" />
                            </button>
                        </div>
                        <div className="mt-6 flow-root">
                            <div className="-my-6 divide-y divide-gray-500/10">
                                <div className="space-y-2 py-6 text-center">
                                    {navigation.map((item) => (
                                        <Link
                                            key={item.name}
                                            href={item.href}
                                            className="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50"
                                            onClick={toggleMenu}
                                        >
                                            {item.name}
                                        </Link>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </DialogPanel>
                </Dialog>
            </div>
        </header>
    )
}