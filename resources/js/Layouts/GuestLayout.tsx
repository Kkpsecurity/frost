import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';
import { PropsWithChildren } from 'react';

export default function Guest({ children }: PropsWithChildren) {
    return (
        <div className="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-light">
            <div className="mb-4">
                <Link href="/">
                    <ApplicationLogo className="text-secondary" style={{ width: '5rem', height: '5rem' }} />
                </Link>
            </div>

            <div className="card shadow" style={{ width: '100%', maxWidth: '400px' }}>
                <div className="card-body p-4">
                    {children}
                </div>
            </div>
        </div>
    );
}
