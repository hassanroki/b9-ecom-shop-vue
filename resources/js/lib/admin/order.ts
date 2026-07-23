import { CheckCircle, Clock, RotateCcw, Truck, Package } from '@lucide/vue';

import type { AdminOrder, OrderStatus } from '@/types/admin';

export const ORDER_STEPS: OrderStatus[] = [
    'pending',
    'processing',
    'shipped',
    'delivered',
];

export type StepColors = {
    done: string;
    active: string;
    upcoming: string;
    icon: typeof Package;
    label: string;
};

export const STEP_CONFIG: Record<string, StepColors> = {
    pending: {
        done: 'bg-amber-500 border-amber-500 text-white',
        active: 'bg-amber-50 border-amber-500 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400',
        upcoming: 'bg-background border-border text-muted-foreground',
        icon: Clock,
        label: 'Pending',
    },

    processing: {
        done: 'bg-blue-500 border-blue-500 text-white',
        active: 'bg-blue-50 border-blue-500 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400',
        upcoming: 'bg-background border-border text-muted-foreground',
        icon: RotateCcw,
        label: 'Processing',
    },

    shipped: {
        done: 'bg-indigo-500 border-indigo-500 text-white',
        active: 'bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400',
        upcoming: 'bg-background border-border text-muted-foreground',
        icon: Truck,
        label: 'Shipped',
    },

    delivered: {
        done: 'bg-emerald-500 border-emerald-500 text-white',
        active: 'bg-emerald-50 border-emerald-500 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400',
        upcoming: 'bg-background border-border text-muted-foreground',
        icon: CheckCircle,
        label: 'Delivered',
    },
};

export const STATUS_BADGE_CLASSES: Record<string, string> = {
    pending:
        'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-800',

    processing:
        'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/30 dark:text-blue-400 dark:border-blue-800',

    shipped:
        'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/30 dark:text-indigo-400 dark:border-indigo-800',

    delivered:
        'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-800',

    cancelled:
        'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-800',
};

export const PAYMENT_BADGE_CLASSES: Record<string, string> = {
    pending:
        'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-800',

    paid: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-800',

    failed: 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-800',

    cancelled:
        'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-800',
};

export function stepState(
    currentStatus: OrderStatus,
    step: OrderStatus,
): 'done' | 'active' | 'upcoming' {
    if (currentStatus === 'cancelled') {
        return 'upcoming';
    }

    const currentIndex = ORDER_STEPS.indexOf(currentStatus);
    const stepIndex = ORDER_STEPS.indexOf(step);

    if (stepIndex < currentIndex) return 'done';
    if (stepIndex === currentIndex) return 'active';

    return 'upcoming';
}

export function stepClasses(currentStatus: OrderStatus, step: OrderStatus) {
    return STEP_CONFIG[step]?.[stepState(currentStatus, step)] ?? '';
}

export function statusBadgeClasses(status: string) {
    return (
        STATUS_BADGE_CLASSES[status] ??
        'bg-muted text-muted-foreground border-border'
    );
}

export function paymentBadgeClasses(status: string) {
    return (
        PAYMENT_BADGE_CLASSES[status] ??
        'bg-muted text-muted-foreground border-border'
    );
}

export function paymentMethodLabel(method: AdminOrder['payment_method']) {
    return method === 'cod' ? 'Cash on Delivery' : 'SSLCommerz';
}

export function formatDate(value: string | null) {
    if (!value) return '—';

    return new Date(value).toLocaleString('en-BD', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
