<script setup lang="ts">
import { ref, watch } from 'vue';
import { Form, Head, Link, router } from '@inertiajs/vue3';
import { Eye, Pencil, Plus, Search, Trash2 } from '@lucide/vue';
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
</script>

<template>
    <Head title="Coupons" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
        >
            <Heading
                title="Coupons"
                description="Manage discount coupons for your store."
            />

            <Button as-child>
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add Coupon
                </Link>
            </Button>
        </div>

        <div
            class="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 p-4 lg:flex-row"
        >
            <div class="relative flex-1">
                <Search
                    class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="search"
                    class="pl-10"
                    placeholder="Search coupon by name or code..."
                />
            </div>

            <Select v-model="status">
                <SelectTrigger class="w-full lg:w-[220px]">
                    <SelectValue placeholder="All Status" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">All Status</SelectItem>
                    <SelectItem value="active">Active</SelectItem>
                    <SelectItem value="inactive">Inactive</SelectItem>
                    <SelectItem value="expired">Expired</SelectItem>
                    <SelectItem value="scheduled">Scheduled</SelectItem>
                </SelectContent>
            </Select>
        </div>

        <div
            class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1200px] text-sm">
                    <thead class="border-b bg-muted/40 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium">Code</th>
                            <th class="px-4 py-3 font-medium">Coupon</th>
                            <th class="px-4 py-3 font-medium">Discount</th>
                            <th class="px-4 py-3 font-medium">Usage</th>
                            <th class="px-4 py-3 font-medium">Validity</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 text-right font-medium">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="coupon in coupons.data"
                            :key="coupon.id"
                            class="border-b last:border-b-0"
                        >
                            <td class="px-4 py-3">
                                <Badge variant="outline">
                                    {{ coupon.code }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">
                                    {{ coupon.name }}
                                </div>
                                <div
                                    v-if="coupon.description"
                                    class="mt-1 text-xs text-muted-foreground"
                                >
                                    {{ coupon.description }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <Badge
                                    :variant="
                                        coupon.type === 'percentage'
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{ coupon.formatted_discount }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">
                                    {{ coupon.used_count }}
                                    <span v-if="coupon.usage_limit">
                                        / {{ coupon.usage_limit }}
                                    </span>
                                    <span v-else class="text-muted-foreground">
                                        / Unlimited
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1 text-xs">
                                    <div>
                                        <span class="font-medium">Start:</span>
                                        {{ coupon.starts_at ?? '-' }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Expire:</span>
                                        {{ coupon.expires_at ?? 'Never' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <Badge
                                    :variant="
                                        coupon.status === 'Active'
                                            ? 'default'
                                            : coupon.status === 'Scheduled'
                                              ? 'secondary'
                                              : 'destructive'
                                    "
                                >
                                    {{ coupon.status }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <Button
                                        as-child
                                        variant="outline"
                                        size="sm"
                                    >
                                        <Link :href="show(coupon.id)">
                                            <Eye class="size-4" />
                                            View
                                        </Link>
                                    </Button>
                                    <Button
                                        as-child
                                        variant="outline"
                                        size="sm"
                                    >
                                        <Link :href="edit(coupon.id)">
                                            <Pencil class="size-4" />
                                            Edit
                                        </Link>
                                    </Button>
                                    <Dialog>
                                        <DialogTrigger as-child>
                                            <Button
                                                variant="destructive"
                                                size="sm"
                                            >
                                                <Trash2 class="size-4" />
                                                Delete
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
                                                    <DialogTitle>
                                                        Delete Coupon?
                                                    </DialogTitle>
                                                    <DialogDescription>
                                                        This will permanently
                                                        remove
                                                        <strong>{{
                                                            coupon.code
                                                        }}</strong>
                                                        from your store.
                                                    </DialogDescription>
                                                </DialogHeader>

                                                <DialogFooter class="gap-2">
                                                    <DialogClose as-child>
                                                        <Button
                                                            type="button"
                                                            variant="secondary"
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
                        <tr v-if="coupons.data.length === 0">
                            <td
                                colspan="7"
                                class="px-4 py-12 text-center text-muted-foreground"
                            >
                                No coupons found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="coupons.links.length > 3"
                class="flex flex-col items-center justify-between gap-4 border-t px-4 py-4 sm:flex-row"
            >
                <div class="text-sm text-muted-foreground">
                    Showing
                    <span class="font-medium">{{ coupons.from ?? 0 }}</span>
                    to
                    <span class="font-medium">{{ coupons.to ?? 0 }}</span>
                    of
                    <span class="font-medium">{{ coupons.total }}</span>
                    coupons
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <template v-for="link in coupons.links" :key="link.label">
                        <Button
                            v-if="link.url"
                            as-child
                            :variant="link.active ? 'default' : 'outline'"
                            size="sm"
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
                            variant="outline"
                            size="sm"
                            disabled
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
