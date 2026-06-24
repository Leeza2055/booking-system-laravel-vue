import type { User } from './auth';

export * from './auth';
export * from './navigation';
export * from './ui';
export interface Service {
    id: number;
    name: string;
    description: string;
    duration_minutes: number;
    price: number;
    is_active: boolean;
}
export interface Schedule {
    id: number;
    day_of_week: number; 
    day_of_week_label: string;
    start_time: string;
    end_time: string;
    slot_duration_minutes: number;
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
    schedules?: Schedule[];
}

export interface Booking {
    id: number;
    provider: Provider;
    service: Service;
    booking_date: string;
    start_time: string;
    end_time: string;
    notes: string | null;
    status: 'pending' | 'confirmed' | 'completed' | 'cancelled';
}


