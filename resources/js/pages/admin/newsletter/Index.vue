<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Mail, Search, X, Download, Trash2 } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import AdminPagination from '@/components/admin/AdminPagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { dashboard } from '@/routes';
import { index, download, destroy } from '@/routes/admin/newsletters';
import type {
    AdminNewsletterFilters,
    AdminNewsletterPaginated,
} from '@/types/admin';

const props = defineProps<{
    newsletters: AdminNewsletterPaginated;
    filters: AdminNewsletterFilters;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Newsletters', href: index() },
        ],
    },
});

// Filter state
const search = ref(props.filters.search || '');
const isDownloading = ref(false);
let debounceTimer: ReturnType<typeof setTimeout>;

function performSearch() {
    router.get(
        index(),
        { search: search.value || undefined },
        { preserveState: true, replace: true },
    );
}

// Debounced typing handler
watch(search, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(performSearch, 350);
});

function handleReset() {
    search.value = '';
    performSearch();
}

async function handleDownload(): Promise<void> {
    if (isDownloading.value) return;

    isDownloading.value = true;

    const url = download().url;
    const query = search.value
        ? `?search=${encodeURIComponent(search.value)}`
        : '';

    try {
        const response = await fetch(url + query, {
            headers: {
                Accept: 'text/csv',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error('Download failed');
        }

        const blob = await response.blob();
        const blobUrl = window.URL.createObjectURL(blob);

        // ফাইলনাম response header থেকে বের করা (fallback সহ)
        const disposition = response.headers.get('Content-Disposition');
        const filenameMatch = disposition?.match(/filename="?([^"]+)"?/);
        const filename = filenameMatch?.[1] ?? 'newsletter-subscribers.csv';

        const link = document.createElement('a');
        link.href = blobUrl;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(blobUrl);
    } catch (error) {
        console.error('CSV download error:', error);
    } finally {
        isDownloading.value = false;
    }
}

function handleDelete(id: number): void {
    if (!confirm('Remove this subscriber from the newsletter list?')) return;

    router.delete(destroy(id).url, {
        preserveScroll: true,
    });
}

function formatDate(value: string): string {
    if (!value) return '—';
    return new Date(value).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="Newsletters" />

    <div class="flex h-full flex-1 flex-col gap-5 p-4 md:p-6">
        <!-- Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Newsletter Subscribers"
                description="View and manage everyone who has subscribed to your newsletter."
            />
            <div class="flex items-center gap-2">
                <Badge variant="outline" class="px-3 py-1 text-xs">
                    Total Subscribers: {{ newsletters.total }}
                </Badge>
                <Button
                    size="sm"
                    variant="outline"
                    :disabled="isDownloading"
                    @click="handleDownload"
                >
                    <Download class="mr-1.5 size-4" />
                    {{ isDownloading ? 'Preparing...' : 'Download CSV' }}
                </Button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div
            class="flex flex-col items-center gap-3 rounded-xl border bg-card p-4 shadow-sm sm:flex-row"
        >
            <div class="relative w-full sm:max-w-md">
                <Search
                    class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="search"
                    type="text"
                    placeholder="Search by email..."
                    class="pr-9 pl-9"
                />
                <button
                    v-if="search"
                    type="button"
                    @click="handleReset"
                    class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                >
                    <X class="size-4" />
                </button>
            </div>

            <div
                v-if="props.filters.search"
                class="flex w-full items-center gap-2 sm:w-auto"
            >
                <Button
                    variant="ghost"
                    size="sm"
                    @click="handleReset"
                    class="text-xs"
                >
                    Clear Filters
                </Button>
            </div>
        </div>

        <!-- Desktop View: Table -->
        <div
            class="hidden overflow-hidden rounded-xl border bg-card shadow-sm md:block"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b bg-muted/50 text-xs tracking-wider text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-6 py-3.5 font-medium">Email</th>
                            <th class="px-6 py-3.5 font-medium">
                                Subscribed On
                            </th>
                            <th class="px-6 py-3.5 font-medium text-right">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr
                            v-for="newsletter in newsletters.data"
                            :key="newsletter.id"
                            class="transition-colors hover:bg-muted/30"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary"
                                    >
                                        <Mail class="size-4" />
                                    </div>
                                    <span class="font-medium text-foreground">
                                        {{ newsletter.email }}
                                    </span>
                                </div>
                            </td>

                            <td
                                class="px-6 py-4 text-xs whitespace-nowrap text-muted-foreground"
                            >
                                {{ formatDate(newsletter.created_at) }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                    @click="handleDelete(newsletter.id)"
                                >
                                    <Trash2 class="size-4" />
                                </Button>
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr v-if="newsletters.data.length === 0">
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div
                                    class="flex flex-col items-center justify-center gap-2"
                                >
                                    <div
                                        class="flex size-12 items-center justify-center rounded-full bg-muted"
                                    >
                                        <Mail
                                            class="size-6 text-muted-foreground"
                                        />
                                    </div>
                                    <h3
                                        class="mt-2 font-semibold text-foreground"
                                    >
                                        No subscribers found
                                    </h3>
                                    <p
                                        class="max-w-sm text-sm text-muted-foreground"
                                    >
                                        {{
                                            search
                                                ? 'No subscribers match your search. Try clearing the search box.'
                                                : 'No one has subscribed to the newsletter yet.'
                                        }}
                                    </p>
                                    <Button
                                        v-if="search"
                                        variant="outline"
                                        size="sm"
                                        @click="handleReset"
                                        class="mt-2"
                                    >
                                        Clear Search
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="newsletters.data.length > 0"
                class="border-t px-6 py-4"
            >
                <AdminPagination
                    :links="newsletters.links"
                    :from="newsletters.from"
                    :to="newsletters.to"
                    :total="newsletters.total"
                />
            </div>
        </div>

        <!-- Mobile View: Cards Layout -->
        <div class="flex flex-col gap-4 md:hidden">
            <div class="grid grid-cols-1 gap-4">
                <div
                    v-for="newsletter in newsletters.data"
                    :key="newsletter.id"
                    class="flex items-center justify-between gap-3 rounded-xl border bg-card p-4 shadow-sm"
                >
                    <div class="flex min-w-0 items-center gap-3">
                        <div
                            class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary"
                        >
                            <Mail class="size-4" />
                        </div>
                        <div class="flex min-w-0 flex-col">
                            <span class="truncate text-sm font-medium">
                                {{ newsletter.email }}
                            </span>
                            <span class="text-[11px] text-muted-foreground">
                                {{ formatDate(newsletter.created_at) }}
                            </span>
                        </div>
                    </div>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="shrink-0 text-destructive hover:bg-destructive/10 hover:text-destructive"
                        @click="handleDelete(newsletter.id)"
                    >
                        <Trash2 class="size-4" />
                    </Button>
                </div>

                <!-- Mobile Empty State -->
                <div
                    v-if="newsletters.data.length === 0"
                    class="rounded-xl border bg-card p-8 text-center"
                >
                    <Mail
                        class="mx-auto mb-2 size-8 text-muted-foreground/60"
                    />
                    <p class="font-medium text-foreground">
                        No subscribers found
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        Try adjusting your search criteria.
                    </p>
                </div>
            </div>

            <!-- Mobile Pagination -->
            <div
                v-if="newsletters.data.length > 0"
                class="rounded-xl border bg-card p-4 shadow-sm"
            >
                <AdminPagination
                    :links="newsletters.links"
                    :from="newsletters.from"
                    :to="newsletters.to"
                    :total="newsletters.total"
                />
            </div>
        </div>
    </div>
</template>
