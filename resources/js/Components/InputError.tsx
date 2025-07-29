import { HTMLAttributes } from 'react';

export default function InputError({ message, className = '', ...props }: HTMLAttributes<HTMLParagraphElement> & { message?: string }) {
    return message ? (
        <div {...props} className={'text-danger small ' + className}>
            {message}
        </div>
    ) : null;
}
