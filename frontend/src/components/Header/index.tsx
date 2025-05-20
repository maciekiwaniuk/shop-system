'use client'

import { useState } from 'react'
import { Dialog, DialogPanel } from '@headlessui/react'
import { Bars3Icon, XMarkIcon } from '@heroicons/react/24/outline'
import Link from 'next/link';
import SearchBar from '@/components/Header/SearchBar';

// var toggleOpen = document.getElementById('toggleOpen')!;
// var toggleClose = document.getElementById('toggleClose')!;
// var collapseMenu = document.getElementById('collapseMenu')!;
//
// function handleClick() {
//     if (collapseMenu.style.display === 'block') {
//         collapseMenu.style.display = 'none';
//     } else {
//         collapseMenu.style.display = 'block';
//     }
// }
//
// toggleOpen.addEventListener('click', handleClick);
// toggleClose.addEventListener('click', handleClick);

// https://readymadeui.com/tailwind-components/header

export default function Header() {
    return (
        <header className="flex shadow-md sm:px-10 px-6 py-3 bg-white min-h-[70px]">
            <div className="flex w-full max-w-screen-xl mx-auto">
                <div
                    className="flex flex-wrap items-center justify-between relative lg:gap-y-4 gap-y-4 gap-x-4 w-full">

                    <div className="flex items-center">
                        <Link href="/frontend/public">
                            Shop system
                        </Link>
                    </div>

                    <SearchBar />

                    <div className="flex items-center space-x-4 max-md:ml-auto">
                        <span className="relative pr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px"
                                className="cursor-pointer fill-gray-800 hover:fill-blue-700 inline-block" viewBox="0 0 512 512">
                                <path
                                    d="M164.96 300.004h.024c.02 0 .04-.004.059-.004H437a15.003 15.003 0 0 0 14.422-10.879l60-210a15.003 15.003 0 0 0-2.445-13.152A15.006 15.006 0 0 0 497 60H130.367l-10.722-48.254A15.003 15.003 0 0 0 105 0H15C6.715 0 0 6.715 0 15s6.715 15 15 15h77.969c1.898 8.55 51.312 230.918 54.156 243.71C131.184 280.64 120 296.536 120 315c0 24.812 20.188 45 45 45h272c8.285 0 15-6.715 15-15s-6.715-15-15-15H165c-8.27 0-15-6.73-15-15 0-8.258 6.707-14.977 14.96-14.996zM477.114 90l-51.43 180H177.032l-40-180zM150 405c0 24.813 20.188 45 45 45s45-20.188 45-45-20.188-45-45-45-45 20.188-45 45zm45-15c8.27 0 15 6.73 15 15s-6.73 15-15 15-15-6.73-15-15 6.73-15 15-15zm167 15c0 24.813 20.188 45 45 45s45-20.188 45-45-20.188-45-45-45-45 20.188-45 45zm45-15c8.27 0 15 6.73 15 15s-6.73 15-15 15-15-6.73-15-15 6.73-15 15-15zm0 0"
                                    data-original="#000000">
                                </path>
                            </svg>
                            <span
                                className="absolute left-auto -ml-1 top-0 rounded-full bg-blue-600 px-1 py-0 text-xs min-w-[15px] text-center text-white"
                            >
                                4
                            </span>
                        </span>

                        <div className="flex ml-auto">
                            <Link
                                href="/login"
                                className="px-4 py-2 text-[15px] rounded-md font-medium text-white bg-blue-600 hover:bg-blue-700 cursor-pointer">
                                Log in
                            </Link>

                            <div id="toggleOpen" className="flex ml-auto lg:hidden">
                                <button className="ml-4 cursor-pointer">
                                    <svg className="w-7 h-7" fill="#000" viewBox="0 0 20 20"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fillRule="evenodd"
                                              d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                              clipRule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="button"
                                className="border-0 outline-0 flex items-center justify-center rounded-full p-2 hover:bg-gray-100 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" className="w-5 h-5 cursor-pointer fill-black"
                                 viewBox="0 0 512 512">
                                <path
                                    d="M337.711 241.3a16 16 0 0 0-11.461 3.988c-18.739 16.561-43.688 25.682-70.25 25.682s-51.511-9.121-70.25-25.683a16.007 16.007 0 0 0-11.461-3.988c-78.926 4.274-140.752 63.672-140.752 135.224v107.152C33.537 499.293 46.9 512 63.332 512h385.336c16.429 0 29.8-12.707 29.8-28.325V376.523c-.005-71.552-61.831-130.95-140.757-135.223zM446.463 480H65.537V376.523c0-52.739 45.359-96.888 104.351-102.8C193.75 292.63 224.055 302.97 256 302.97s62.25-10.34 86.112-29.245c58.992 5.91 104.351 50.059 104.351 102.8zM256 234.375a117.188 117.188 0 1 0-117.188-117.187A117.32 117.32 0 0 0 256 234.375zM256 32a85.188 85.188 0 1 1-85.188 85.188A85.284 85.284 0 0 1 256 32z"
                                    data-original="#000000"/>
                            </svg>
                        </button>

                        <button id="toggleOpen" className="cursor-pointer">
                            <svg className="w-8 h-8" fill="#000" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fillRule="evenodd"
                                      d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                      clipRule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="collapseMenu"
                     className="hidden before:fixed before:bg-black before:opacity-40 before:inset-0 max-lg:before:z-50">
                    <button id="toggleClose"
                            className="fixed top-2 right-4 z-[100] rounded-full bg-white w-9 h-9 flex items-center justify-center border border-gray-200 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" className="w-3.5 h-3.5 fill-black"
                             viewBox="0 0 320.591 320.591">
                            <path
                                d="M30.391 318.583a30.37 30.37 0 0 1-21.56-7.288c-11.774-11.844-11.774-30.973 0-42.817L266.643 10.665c12.246-11.459 31.462-10.822 42.921 1.424 10.362 11.074 10.966 28.095 1.414 39.875L51.647 311.295a30.366 30.366 0 0 1-21.256 7.288z"
                                data-original="#000000"></path>
                            <path
                                d="M287.9 318.583a30.37 30.37 0 0 1-21.257-8.806L8.83 51.963C-2.078 39.225-.595 20.055 12.143 9.146c11.369-9.736 28.136-9.736 39.504 0l259.331 257.813c12.243 11.462 12.876 30.679 1.414 42.922-.456.487-.927.958-1.414 1.414a30.368 30.368 0 0 1-23.078 7.288z"
                                data-original="#000000"></path>
                        </svg>
                    </button>

                    <ul
                        className="block space-x-4 space-y-3 fixed bg-white w-1/2 min-w-[300px] top-0 left-0 p-4 h-full shadow-md overflow-auto z-50">
                        <li className="pb-4 px-3">
                            <a href="javascript:void(0)"><img src="https://readymadeui.com/readymadeui.svg" alt="logo"
                                                              className="w-36"/>
                            </a>
                        </li>
                        <li className="border-b border-gray-300 pb-4 px-3 hidden">
                            <a href="javascript:void(0)"><img src="https://readymadeui.com/readymadeui.svg" alt="logo"
                                                              className="w-36"/>
                            </a>
                        </li>
                        <li className="border-b border-gray-300 py-3 px-3">
                            <a href='javascript:void(0)'
                               className="hover:text-blue-700 text-blue-700 block font-medium text-base">Home</a>
                        </li>
                        <li className="border-b border-gray-300 py-3 px-3"><a href='javascript:void(0)'
                                                                              className="hover:text-blue-700 text-slate-900 block font-medium text-base">Team</a>
                        </li>
                        <li className="border-b border-gray-300 py-3 px-3"><a href='javascript:void(0)'
                                                                              className="hover:text-blue-700 text-slate-900 block font-medium text-base">Feature</a>
                        </li>
                        <li className="border-b border-gray-300 py-3 px-3"><a href='javascript:void(0)'
                                                                              className="hover:text-blue-700 text-slate-900 block font-medium text-base">Blog</a>
                        </li>
                        <li className="border-b border-gray-300 py-3 px-3"><a href='javascript:void(0)'
                                                                              className="hover:text-blue-700 text-slate-900 block font-medium text-base">About</a>
                        </li>
                        <li className="border-b border-gray-300 py-3 px-3"><a href='javascript:void(0)'
                                                                              className="hover:text-blue-700 text-slate-900 block font-medium text-base">Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>


    )
}
