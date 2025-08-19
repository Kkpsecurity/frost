// Global type declarations for Laravel application

import { AxiosStatic } from 'axios';

declare global {
    interface Window {
        axios: AxiosStatic;
        Pusher?: any;
        Echo?: any;
    }
}

export {};
