import type { User } from './auth';

export * from './auth';
export * from './navigation';
export * from './ui';
export interface Service {
    id: number;
    name: string;
    duration_minutes: number;
    price: number;
    is_active: boolean;
}
export interface Provider {
    id: number;
    user: User;
    department: string;
    specialization: string;
    bio: string | null;
    base_fee: number;
    is_active: boolean;  
    services?: Service[];
}


