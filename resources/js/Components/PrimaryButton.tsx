import { ButtonHTMLAttributes } from 'react';

export default function PrimaryButton({ className = '', disabled, children, ...props }: ButtonHTMLAttributes<HTMLButtonElement>) {
    return (
        <button
            {...props}
            className={
                `btn btn-primary ${disabled && 'disabled'} ` + className
            }
            disabled={disabled}
        >
            {children}
        </button>
    );
}
