export default function Footer() {
    return (
        <>
            <footer className="tracking-wide bg-gray-50 px-10 pt-12 pb-6">
                <div className="flex flex-wrap max-md:flex-col gap-4">
                    <ul className="md:flex md:space-x-6 max-md:space-y-2">
                        <li>
                            <a href='javascript:void(0)'
                               className="hover:text-slate-900 text-slate-600 text-sm font-normal">Terms of Service</a>
                        </li>
                        <li>
                            <a href='javascript:void(0)'
                               className="hover:text-slate-900 text-slate-600 text-sm font-normal">Privacy Policy</a>
                        </li>
                        <li>
                            <a href='javascript:void(0)'
                               className="hover:text-slate-900 text-slate-600 text-sm font-normal">Security</a>
                        </li>
                    </ul>

                    <p className="text-slate-600 text-sm md:ml-auto">© Shop system - Maciek Iwaniuk - All rights reserved.</p>
                </div>
            </footer>
        </>
    );
}