<script setup lang="ts">
import { ref, watch } from 'vue';
import { Form, Head, Link, router } from '@inertiajs/vue3';
import {
    Eye,
    Pencil,
    Plus,
    Search,
    Trash2,
    X,
    Tag,
    Calendar,
    Percent,
} from 'lucide-vue-next';
import CouponController from '@/actions/App/Http/Controllers/Admin/CouponController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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
import { create, edit, index, show } from '@/routes/admin/coupons';
import type { AdminCoupon, Paginated } from '@/types/admin';

const props = defineProps<{
    coupons: Paginated<AdminCoupon>;
    filters: {
        search?: string;
        status?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Coupons',
                href: index(),
            },
        ],
    },
});

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? 'all');

let timer: ReturnType<typeof setTimeout>;

watch([search, status], () => {
    clearTimeout(timer);

    timer = setTimeout(() => {
        router.get(
            index(),
            {
                search: search.value,
                status: status.value === 'all' ? '' : status.value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    }, 400);
});

const clearSearch = () => {
    search.value = '';
};
</script>

<template>
    <Head title="Coupons" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8">
        <!-- Top Bar: Header & Primary Actions -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Coupons"
                description="Manage and monitor discount coupons for your store."
            />

            <Button
                as-child
                class="shrink-0 shadow-sm transition-all hover:shadow"
            >
                <Link :href="create()">
                    <Plus class="mr-1.5 size-4" />
                    Add Coupon
                </Link>
            </Button>
        </div>

        <!-- Filter & Search Controls -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <Search
                    class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="search"
                    class="bg-background pr-9 pl-9 shadow-xs transition-shadow focus-visible:ring-1"
                    placeholder="Search by name or code..."
                />
                <button
                    v-if="search"
                    type="button"
                    @click="clearSearch"
                    class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                >
                    <X class="size-4" />
                </button>
            </div>

            <Select v-model="status">
                <SelectTrigger class="w-full bg-background shadow-xs sm:w-45">
                    <SelectValue placeholder="All Status" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">All Statuses</SelectItem>
                    <SelectItem value="active">Active</SelectItem>
                    <SelectItem value="inactive">Inactive</SelectItem>
                    <SelectItem value="expired">Expired</SelectItem>
                    <SelectItem value="scheduled">Scheduled</SelectItem>
                </SelectContent>
            </Select>
        </div>

        <!-- Content Card Area -->
        <div
            class="overflow-hidden rounded-xl border bg-card text-card-foreground shadow-xs"
        >
            <!-- Desktop View: Table -->
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b bg-muted/30 text-xs tracking-wider text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-6 py-3.5 font-semibold">Code</th>
                            <th class="px-6 py-3.5 font-semibold">Coupon</th>
                            <th class="px-6 py-3.5 font-semibold">Discount</th>
                            <th class="px-6 py-3.5 font-semibold">Usage</th>
                            <th class="px-6 py-3.5 font-semibold">Validity</th>
                            <th class="px-6 py-3.5 font-semibold">Status</th>
                            <th class="px-6 py-3.5 text-right font-semibold">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="coupon in coupons.data"
                            :key="coupon.id"
                            class="transition-colors hover:bg-muted/20"
                        >
                            <!-- Code -->
                            <td class="px-6 py-4 font-mono font-medium">
                                <Badge
                                    variant="secondary"
                                    class="font-mono text-xs tracking-wide"
                                >
                                    {{ coupon.code }}
                                </Badge>
                            </td>

                            <!-- Coupon Name & Desc -->
                            <td class="px-6 py-4">
                                <div class="font-medium text-foreground">
                                    {{ coupon.name }}
                                </div>
                                <div
                                    v-if="coupon.description"
                                    class="mt-0.5 line-clamp-1 text-xs text-muted-foreground"
                                >
                                    {{ coupon.description }}
                                </div>
                            </td>

                            <!-- Discount -->
                            <td class="px-6 py-4">
                                <Badge
                                    :variant="
                                        coupon.type === 'percentage'
                                            ? 'default'
                                            : 'outline'
                                    "
                                    class="font-semibold"
                                >
                                    {{ coupon.formatted_discount }}
                                </Badge>
                            </td>

                            <!-- Usage -->
                            <td class="px-6 py-4 text-sm">
                                <span class="font-medium text-foreground">{{
                                    coupon.used_count
                                }}</span>
                                <span
                                    v-if="coupon.usage_limit"
                                    class="text-muted-foreground"
                                >
                                    / {{ coupon.usage_limit }}
                                </span>
                                <span
                                    v-else
                                    class="text-xs text-muted-foreground"
                                >
                                    / ∞
                                </span>
                            </td>

                            <!-- Validity -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-0.5 text-xs">
                                    <span class="text-muted-foreground">
                                        From:
                                        <strong
                                            class="font-normal text-foreground"
                                            >{{
                                                coupon.starts_at ?? 'Immediate'
                                            }}</strong
                                        >
                                    </span>
                                    <span class="text-muted-foreground">
                                        Until:
                                        <strong
                                            class="font-normal text-foreground"
                                            >{{
                                                coupon.expires_at ?? 'Never'
                                            }}</strong
                                        >
                                    </span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                <Badge
                                    :variant="
                                        coupon.status === 'Active'
                                            ? 'default'
                                            : coupon.status === 'Scheduled'
                                              ? 'secondary'
                                              : 'destructive'
                                    "
                                    class="capitalize"
                                >
                                    {{ coupon.status }}
                                </Badge>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div
                                    class="flex items-center justify-end gap-1.5"
                                >
                                    <Button
                                        as-child
                                        variant="ghost"
                                        size="icon"
                                        class="size-8"
                                    >
                                        <Link
                                            :href="show(coupon.id)"
                                            title="View"
                                        >
                                            <Eye class="size-4" />
                                        </Link>
                                    </Button>

                                    <Button
                                        as-child
                                        variant="ghost"
                                        size="icon"
                                        class="size-8"
                                    >
                                        <Link
                                            :href="edit(coupon.id)"
                                            title="Edit"
                                        >
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>

                                    <Dialog>
                                        <DialogTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                class="size-8 text-destructive hover:text-destructive"
                                            >
                                                <Trash2 class="size-4" />
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent>
                                            <Form
                                                v-bind="
                                                    CouponController.destroy.form(
                                                        coupon.id,
                                                    )
                                                "
                                                :options="{
                                                    preserveScroll: true,
                                                }"
                                                v-slot="{ processing }"
                                            >
                                                <DialogHeader>
                                                    <DialogTitle
                                                        >Delete
                                                        Coupon?</DialogTitle
                                                    >
                                                    <DialogDescription>
                                                        Are you sure you want to
                                                        delete
                                                        <strong
                                                            class="text-foreground"
                                                            >{{
                                                                coupon.code
                                                            }}</strong
                                                        >? This action cannot be
                                                        undone.
                                                    </DialogDescription>
                                                </DialogHeader>

                                                <DialogFooter
                                                    class="mt-4 gap-2"
                                                >
                                                    <DialogClose as-child>
                                                        <Button
                                                            type="button"
                                                            variant="outline"
                                                        >
                                                            Cancel
                                                        </Button>
                                                    </DialogClose>
                                                    <Button
                                                        type="submit"
                                                        variant="destructive"
                                                        :disabled="processing"
                                                    >
                                                        Delete
                                                    </Button>
                                                </DialogFooter>
                                            </Form>
                                        </DialogContent>
                                    </Dialog>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: Responsive Cards -->
            <div class="divide-y divide-border md:hidden">
                <div
                    v-for="coupon in coupons.data"
                    :key="coupon.id"
                    class="flex flex-col gap-3 p-4 transition-colors hover:bg-muted/10"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <Badge
                                    variant="secondary"
                                    class="font-mono text-xs"
                                >
                                    {{ coupon.code }}
                                </Badge>
                                <Badge
                                    :variant="
                                        coupon.status === 'Active'
                                            ? 'default'
                                            : coupon.status === 'Scheduled'
                                              ? 'secondary'
                                              : 'destructive'
                                    "
                                    class="px-1.5 py-0 text-[10px]"
                                >
                                    {{ coupon.status }}
                                </Badge>
                            </div>
                            <h4 class="mt-1.5 font-medium text-foreground">
                                {{ coupon.name }}
                            </h4>
                        </div>
                        <Badge
                            :variant="
                                coupon.type === 'percentage'
                                    ? 'default'
                                    : 'outline'
                            "
                        >
                            {{ coupon.formatted_discount }}
                        </Badge>
                    </div>

                    <p
                        v-if="coupon.description"
                        class="line-clamp-2 text-xs text-muted-foreground"
                    >
                        {{ coupon.description }}
                    </p>

                    <div
                        class="grid grid-cols-2 gap-2 pt-1 text-xs text-muted-foreground"
                    >
                        <div class="flex items-center gap-1.5">
                            <Tag class="size-3.5 shrink-0" />
                            <span>
                                Used:
                                <strong class="font-normal text-foreground">{{
                                    coupon.used_count
                                }}</strong>
                                <span v-if="coupon.usage_limit"
                                    >/{{ coupon.usage_limit }}</span
                                >
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <Calendar class="size-3.5 shrink-0" />
                            <span class="truncate">
                                {{
                                    coupon.expires_at
                                        ? `Exp: ${coupon.expires_at}`
                                        : 'Never expires'
                                }}
                            </span>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end gap-2 border-t border-border/50 pt-2"
                    >
                        <Button
                            as-child
                            variant="outline"
                            size="sm"
                            class="h-8 text-xs"
                        >
                            <Link :href="show(coupon.id)">
                                <Eye class="mr-1 size-3.5" /> View
                            </Link>
                        </Button>
                        <Button
                            as-child
                            variant="outline"
                            size="sm"
                            class="h-8 text-xs"
                        >
                            <Link :href="edit(coupon.id)">
                                <Pencil class="mr-1 size-3.5" /> Edit
                            </Link>
                        </Button>

                        <Dialog>
                            <DialogTrigger as-child>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    class="h-8 text-xs text-destructive hover:bg-destructive/10"
                                >
                                    <Trash2 class="mr-1 size-3.5" /> Delete
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <Form
                                    v-bind="
                                        CouponController.destroy.form(coupon.id)
                                    "
                                    :options="{ preserveScroll: true }"
                                    v-slot="{ processing }"
                                >
                                    <DialogHeader>
                                        <DialogTitle
                                            >Delete Coupon?</DialogTitle
                                        >
                                        <DialogDescription>
                                            This will permanently delete
                                            <strong>{{ coupon.code }}</strong
                                            >.
                                        </DialogDescription>
                                    </DialogHeader>
                                    <DialogFooter class="mt-4 gap-2">
                                        <DialogClose as-child>
                                            <Button
                                                type="button"
                                                variant="outline"
                                                >Cancel</Button
                                            >
                                        </DialogClose>
                                        <Button
                                            type="submit"
                                            variant="destructive"
                                            :disabled="processing"
                                        >
                                            Delete
                                        </Button>
                                    </DialogFooter>
                                </Form>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="coupons.data.length === 0"
                class="flex flex-col items-center justify-center p-12 text-center"
            >
                <div
                    class="flex size-12 items-center justify-center rounded-full bg-muted"
                >
                    <Tag class="size-6 text-muted-foreground" />
                </div>
                <h3 class="mt-4 text-base font-semibold text-foreground">
                    No coupons found
                </h3>
                <p class="mt-1 text-sm text-muted-foreground">
                    Try adjusting your search query or filter options.
                </p>
            </div>

            <!-- Pagination Container -->
            <div
                v-if="coupons.links.length > 3"
                class="flex flex-col items-center justify-between gap-4 border-t px-6 py-4 sm:flex-row"
            >
                <div class="text-xs text-muted-foreground">
                    Showing
                    <span class="font-medium text-foreground">{{
                        coupons.from ?? 0
                    }}</span>
                    to
                    <span class="font-medium text-foreground">{{
                        coupons.to ?? 0
                    }}</span>
                    of
                    <span class="font-medium text-foreground">{{
                        coupons.total
                    }}</span>
                    results
                </div>

                <div class="flex flex-wrap items-center gap-1.5">
                    <template v-for="link in coupons.links" :key="link.label">
                        <Button
                            v-if="link.url"
                            as-child
                            :variant="link.active ? 'default' : 'outline'"
                            size="sm"
                            class="h-8 min-w-8 px-2.5 text-xs"
                        >
                            <Link
                                :href="link.url"
                                preserve-scroll
                                preserve-state
                                v-html="link.label"
                            />
                        </Button>

                        <Button
                            v-else
                            variant="ghost"
                            size="sm"
                            disabled
                            class="h-8 min-w-8 px-2.5 text-xs"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
