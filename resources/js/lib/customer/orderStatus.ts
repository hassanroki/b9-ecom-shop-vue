export function formatDate(dateString: string | null): string {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

export function getStatusBadgeClass(status: string): string {
    const classes: Record<string, string> = {
        pending: 'bg-yellow-50 text-yellow-700 border border-yellow-200',
        processing: 'bg-blue-50 text-blue-700 border border-blue-200',
        completed: 'bg-green-50 text-green-700 border border-green-200',
        cancelled: 'bg-red-50 text-red-700 border border-red-200',
    };
    return classes[status] || 'bg-gray-50 text-gray-700 border border-gray-200';
}

export function getPaymentStatusBadgeClass(paymentStatus: string): string {
    const classes: Record<string, string> = {
        pending: 'bg-amber-50 text-amber-700 border border-amber-200',
        paid: 'bg-green-50 text-green-700 border border-green-200',
        failed: 'bg-red-50 text-red-700 border border-red-200',
    };
    return (
        classes[paymentStatus] ||
        'bg-gray-50 text-gray-700 border border-gray-200'
    );
}
