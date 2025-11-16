export function Footer() {
    return (
        <footer className="bg-white">
            <div className="border-t border-gray-200">
                <div className="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
                    <p className="text-center text-sm text-gray-500">
                        Maciek Iwaniuk - {new Date().getFullYear()} &copy; All rights reserved
                    </p>
                </div>
            </div>
        </footer>
    );
}

