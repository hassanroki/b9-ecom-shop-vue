<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Star, Search, X, Trash2, Check, RotateCcw } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
import AdminPagination from '@/components/admin/AdminPagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { dashboard } from '@/routes';
import { index, approve, reject, destroy } from '@/routes/admin/review';

interface ReviewItem {
    id: number;
    rating: number;
    comment: string;
    is_approved: boolean;
    approved_at: string | null;
    created_at: string;
    product: { id: number; name: string; slug: string } | null;
    user: { name: string; email: string } | null;
    order_number: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Paginated<T> {
    data: T[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    reviews: Paginated<ReviewItem>;
    filters: {
        search?: string;
        status?: string;
        rating?: string;
    };
    summary: {
        total: number;
        pending: number;
        approved: number;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Reviews', href: index() },
        ],
    },
});

// Filter state
const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const rating = ref(props.filters.rating || '');
let debounceTimer: ReturnType<typeof setTimeout>;

function applyFilters(): void {
    router.get(
        index(),
        {
            search: search.value || undefined,
            status: status.value || undefined,
            rating: rating.value || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

watch(search, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(applyFilters, 350);
});

watch([status, rating], applyFilters);

function handleReset(): void {
    search.value = '';
    status.value = '';
    rating.value = '';
    applyFilters();
}

function approveReview(id: number): void {
    router.patch(approve(id).url, {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Review approved'),
        onError: () => toast.error('Failed to approve review'),
    });
}

function rejectReview(id: number): void {
    router.patch(reject(id).url, {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Review marked as pending'),
        onError: () => toast.error('Failed to update review'),
    });
}

function deleteReview(id: number): void {
    router.delete(destroy(id).url, {
        preserveScroll: true,
        onSuccess: () => toast.success('Review deleted'),
        onError: () => toast.error('Failed to delete review'),
    });
}
</script>

<template>
    <Head title="Reviews" />

    <div class="flex h-full flex-1 flex-col gap-5 p-4 md:p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading
                title="Reviews"
                description="Moderate and manage customer product reviews."
            />
            <div class="flex flex-wrap items-center gap-2">
                <Badge variant="outline" class="px-3 py-1 text-xs">
                    Total: {{ summary.total }}
                </Badge>
                <Badge variant="outline" class="border-amber-200 bg-amber-50 px-3 py-1 text-xs text-amber-700">
                    Pending: {{ summary.pending }}
                </Badge>
                <Badge variant="outline" class="border-green-200 bg-green-50 px-3 py-1 text-xs text-green-700">
                    Approved: {{ summary.approved }}
                </Badge>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-col items-center gap-3 rounded-xl border bg-card p-4 shadow-sm sm:flex-row">
            <div class="relative w-full sm:max-w-md">
                <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    v-model="search"
                    type="text"
                    placeholder="Search by comment, product or customer..."
                    class="pr-9 pl-9"
                />
                <button
                    v-if="search"
                    type="button"
                    @click="search = ''"
                    class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                >
                    <X class="size-4" />
                </button>
            </div>

            <select
                v-model="status"
                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-xs focus:ring-2 focus:ring-ring focus:outline-none sm:w-auto"
            >
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
            </select>

            <select
                v-model="rating"
                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-xs focus:ring-2 focus:ring-ring focus:outline-none sm:w-auto"
            >
                <option value="">All Ratings</option>
                <option v-for="star in [5, 4, 3, 2, 1]" :key="star" :value="star">{{ star }} Star</option>
            </select>

            <div
                v-if="props.filters.search || props.filters.status || props.filters.rating"
                class="flex w-full items-center gap-2 sm:w-auto"
            >
                <Button variant="ghost" size="sm" @click="handleReset" class="text-xs">
                    Clear Filters
                </Button>
            </div>
        </div>

        <!-- Desktop View: Table -->
        <div class="hidden overflow-hidden rounded-xl border bg-card shadow-sm md:block">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-muted/50 text-xs tracking-wider text-muted-foreground uppercase">
                        <tr>
                            <th class="px-6 py-3.5 font-medium">Product</th>
                            <th class="px-6 py-3.5 font-medium">Customer</th>
                            <th class="px-6 py-3.5 font-medium">Rating</th>
                            <th class="px-6 py-3.5 font-medium">Comment</th>
                            <th class="px-6 py-3.5 font-medium">Status</th>
                            <th class="px-6 py-3.5 font-medium">Date</th>
                            <th class="px-6 py-3.5 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr
                            v-for="review in reviews.data"
                            :key="review.id"
                            class="transition-colors hover:bg-muted/30"
                        >
                            <td class="px-6 py-4">
                                <Link
                                    v-if="review.product"
                                    :href="`/products/${review.product.slug}`"
                                    target="_blank"
                                    class="font-medium text-foreground hover:underline"
                                >
                                    {{ review.product.name }}
                                </Link>
                                <span v-else class="text-muted-foreground">Deleted product</span>
                            </td>

                            <td class="px-6 py-4">
                                <p class="font-medium text-foreground">{{ review.user?.name ?? 'N/A' }}</p>
                                <p class="text-xs text-muted-foreground">{{ review.user?.email }}</p>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-0.5">
                                    <Star
                                        v-for="star in 5"
                                        :key="star"
                                        class="size-4"
                                        :class="star <= review.rating ? 'fill-amber-400 text-amber-400' : 'text-muted-foreground/30'"
                                    />
                                </span>
                            </td>

                            <td class="max-w-xs px-6 py-4">
                                <p class="line-clamp-2 text-muted-foreground" :title="review.comment">
                                    {{ review.comment }}
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <Badge
                                    variant="outline"
                                    :class="review.is_approved
                                        ? 'border-green-200 bg-green-50 text-green-700'
                                        : 'border-amber-200 bg-amber-50 text-amber-700'"
                                >
                                    {{ review.is_approved ? 'Approved' : 'Pending' }}
                                </Badge>
                            </td>

                            <td class="px-6 py-4 text-xs whitespace-nowrap text-muted-foreground">
                                {{ review.created_at }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <Button
                                        v-if="!review.is_approved"
                                        variant="ghost"
                                        size="icon"
                                        class="size-8 text-green-600 hover:bg-green-100 hover:text-green-700"
                                        :aria-label="`Approve review from ${review.user?.name}`"
                                        @click="approveReview(review.id)"
                                    >
                                        <Check class="size-4" />
                                    </Button>
                                    <Button
                                        v-else
                                        variant="ghost"
                                        size="icon"
                                        class="size-8 text-amber-600 hover:bg-amber-100 hover:text-amber-700"
                                        :aria-label="`Unapprove review from ${review.user?.name}`"
                                        @click="rejectReview(review.id)"
                                    >
                                        <RotateCcw class="size-4" />
                                    </Button>

                                    <Dialog>
                                        <DialogTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                class="size-8 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                                :aria-label="`Delete review from ${review.user?.name}`"
                                                :data-test="`delete-review-${review.id}`"
                                            >
                                                <Trash2 class="size-4" />
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent>
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle>Delete review?</DialogTitle>
                                                <DialogDescription>
                                                    This will permanently remove the review by
                                                    <strong>{{ review.user?.name ?? 'this customer' }}</strong>
                                                    for <strong>{{ review.product?.name ?? 'this product' }}</strong>.
                                                    This action cannot be undone.
                                                </DialogDescription>
                                            </DialogHeader>

                                            <DialogFooter class="gap-2">
                                                <DialogClose as-child>
                                                    <Button type="button" variant="secondary">Cancel</Button>
                                                </DialogClose>
                                                <DialogClose as-child>
                                                    <Button
                                                        type="button"
                                                        variant="destructive"
                                                        :data-test="`confirm-delete-review-${review.id}`"
                                                        @click="deleteReview(review.id)"
                                                    >
                                                        Delete
                                                    </Button>
                                                </DialogClose>
                                            </DialogFooter>
                                        </DialogContent>
                                    </Dialog>
                                </div>
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr v-if="reviews.data.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="flex size-12 items-center justify-center rounded-full bg-muted">
                                        <Star class="size-6 text-muted-foreground" />
                                    </div>
                                    <h3 class="mt-2 font-semibold text-foreground">No reviews found</h3>
                                    <p class="max-w-sm text-sm text-muted-foreground">
                                        {{
                                            search || status || rating
                                                ? 'No reviews match your filters. Try clearing them.'
                                                : 'No customer has submitted a review yet.'
                                        }}
                                    </p>
                                    <Button
                                        v-if="search || status || rating"
                                        variant="outline"
                                        size="sm"
                                        @click="handleReset"
                                        class="mt-2"
                                    >
                                        Clear Filters
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="reviews.data.length > 0" class="border-t px-6 py-4">
                <AdminPagination
                    :links="reviews.links"
                    :from="reviews.from"
                    :to="reviews.to"
                    :total="reviews.total"
                />
            </div>
        </div>

        <!-- Mobile View: Cards Layout -->
        <div class="flex flex-col gap-4 md:hidden">
            <div class="grid grid-cols-1 gap-4">
                <div
                    v-for="review in reviews.data"
                    :key="review.id"
                    class="rounded-xl border bg-card p-4 shadow-sm"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-foreground">
                                {{ review.product?.name ?? 'Deleted product' }}
                            </p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ review.user?.name }} · {{ review.user?.email }}
                            </p>
                        </div>
                        <Badge
                            variant="outline"
                            class="shrink-0"
                            :class="review.is_approved
                                ? 'border-green-200 bg-green-50 text-green-700'
                                : 'border-amber-200 bg-amber-50 text-amber-700'"
                        >
                            {{ review.is_approved ? 'Approved' : 'Pending' }}
                        </Badge>
                    </div>

                    <div class="mt-2 flex items-center gap-0.5">
                        <Star
                            v-for="star in 5"
                            :key="star"
                            class="size-4"
                            :class="star <= review.rating ? 'fill-amber-400 text-amber-400' : 'text-muted-foreground/30'"
                        />
                    </div>

                    <p class="mt-2 text-sm text-muted-foreground">{{ review.comment }}</p>
                    <p class="mt-2 text-[11px] text-muted-foreground">{{ review.created_at }}</p>

                    <div class="mt-3 flex items-center gap-2 border-t border-border pt-3">
                        <Button
                            v-if="!review.is_approved"
                            variant="outline"
                            size="sm"
                            class="flex-1 text-green-700"
                            @click="approveReview(review.id)"
                        >
                            <Check class="size-4" />
                            Approve
                        </Button>
                        <Button
                            v-else
                            variant="outline"
                            size="sm"
                            class="flex-1 text-amber-700"
                            @click="rejectReview(review.id)"
                        >
                            <RotateCcw class="size-4" />
                            Unapprove
                        </Button>

                        <Dialog>
                            <DialogTrigger as-child>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="size-9 shrink-0 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                    :aria-label="`Delete review from ${review.user?.name}`"
                                    :data-test="`delete-review-${review.id}`"
                                >
                                    <Trash2 class="size-4" />
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader class="space-y-3">
                                    <DialogTitle>Delete review?</DialogTitle>
                                    <DialogDescription>
                                        This will permanently remove the review by
                                        <strong>{{ review.user?.name ?? 'this customer' }}</strong>
                                        for <strong>{{ review.product?.name ?? 'this product' }}</strong>.
                                        This action cannot be undone.
                                    </DialogDescription>
                                </DialogHeader>

                                <DialogFooter class="gap-2">
                                    <DialogClose as-child>
                                        <Button type="button" variant="secondary">Cancel</Button>
                                    </DialogClose>
                                    <DialogClose as-child>
                                        <Button
                                            type="button"
                                            variant="destructive"
                                            :data-test="`confirm-delete-review-${review.id}`"
                                            @click="deleteReview(review.id)"
                                        >
                                            Delete
                                        </Button>
                                    </DialogClose>
                                </DialogFooter>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>

                <!-- Mobile Empty State -->
                <div v-if="reviews.data.length === 0" class="rounded-xl border bg-card p-8 text-center">
                    <Star class="mx-auto mb-2 size-8 text-muted-foreground/60" />
                    <p class="font-medium text-foreground">No reviews found</p>
                    <p class="mt-1 text-xs text-muted-foreground">Try adjusting your filters.</p>
                </div>
            </div>

            <!-- Mobile Pagination -->
            <div v-if="reviews.data.length > 0" class="rounded-xl border bg-card p-4 shadow-sm">
                <AdminPagination
                    :links="reviews.links"
                    :from="reviews.from"
                    :to="reviews.to"
                    :total="reviews.total"
                />
            </div>
        </div>
    </div>
</template>
