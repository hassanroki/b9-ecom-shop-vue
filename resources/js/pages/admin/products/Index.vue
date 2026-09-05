<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Eye, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import ProductController from '@/actions/App/Http/Controllers/Admin/ProductController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
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
import { formatTaka } from '@/lib/shop/currency';
import { dashboard } from '@/routes';
import { create, edit, index, show } from '@/routes/admin/products';
import type { AdminProductListItem } from '@/types/admin';

defineProps<{
    products: AdminProductListItem[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Products', href: index() },
        ],
    },
});
</script>

<template>

    <Head title="Products" />

    <div class="flex h-full flex-1 flex-col gap-5 p-4 md:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading title="Products" description="Manage your store catalog" />

            <Button as-child class="bg-primary text-primary-foreground hover:bg-primary/90">
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add product
                </Link>
            </Button>
        </div>

        <!-- Empty state -->
        <div v-if="products.length === 0" class="rounded-xl border border-border bg-card py-16 text-center shadow-sm">
            <p class="text-sm font-medium text-foreground">No products yet</p>
            <p class="mt-1 text-sm text-muted-foreground">
                Create your first product to get started.
            </p>
            <Button as-child class="mt-4 bg-primary text-primary-foreground hover:bg-primary/90">
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add product
                </Link>
            </Button>
        </div>

        <template v-else>
            <!-- Mobile: card list -->
            <div class="flex flex-col gap-3 md:hidden">
                <div v-for="product in products" :key="product.id"
                    class="rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="size-14 shrink-0 overflow-hidden rounded-lg border border-border bg-muted">
                            <img v-if="product.image" :src="product.image" :alt="product.name"
                                class="size-full object-cover" />
                            <div v-else
                                class="flex size-full items-center justify-center text-xs text-muted-foreground">
                                —
                            </div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-foreground">
                                {{ product.name }}
                            </p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ product.slug }}
                            </p>

                            <div class="mt-1.5 flex items-center gap-2">
                                <span class="text-sm font-semibold text-foreground">{{ formatTaka(product.price)
                                    }}</span>
                                <span v-if="product.compare_at_price"
                                    class="text-xs text-muted-foreground line-through">
                                    {{ formatTaka(product.compare_at_price) }}
                                </span>
                            </div>

                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="product.is_active
                                            ? 'bg-[#00BC7D]/10 text-[#00754F]'
                                            : 'bg-muted text-muted-foreground'
                                        ">
                                    <span class="size-1.5 rounded-full" :class="product.is_active
                                            ? 'bg-[#00BC7D]'
                                            : 'bg-muted-foreground/50'
                                        " />
                                    {{
                                        product.is_active
                                            ? 'Active'
                                            : 'Inactive'
                                    }}
                                </span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="product.stock_status === 'in_stock'
                                        ? 'bg-[#00BC7D]/10 text-[#00754F]'
                                        : 'bg-destructive/10 text-destructive'
                                    ">
                                    {{
                                        product.stock_status === 'in_stock'
                                            ? 'In stock'
                                            : 'Out of stock'
                                    }}
                                </span>
                                <span v-if="product.is_featured"
                                    class="rounded-full border border-border px-2 py-0.5 text-xs font-medium text-muted-foreground">
                                    Featured
                                </span>
                                <span v-if="product.is_best_seller"
                                    class="rounded-full border border-border px-2 py-0.5 text-xs font-medium text-muted-foreground">
                                    Best seller
                                </span>
                            </div>

                            <div class="mt-2 flex gap-4 text-xs text-muted-foreground">
                                <span>{{ product.category.name
                                }}<template v-if="product.category.is_deleted">
                                        (deleted)</template></span>
                                <span v-if="product.brand">{{ product.brand.name
                                }}<template v-if="product.brand.is_deleted">
                                        (deleted)</template></span>
                                <span>Sold: {{ product.sold_count }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center gap-2 border-t border-border pt-3">
                        <Button as-child variant="outline" size="sm" class="flex-1">
                            <Link :href="show(product.id)">
                                <Eye class="size-4" />
                                View
                            </Link>
                        </Button>
                        <Button as-child variant="outline" size="sm" class="flex-1">
                            <Link :href="edit(product.id)">
                                <Pencil class="size-4" />
                                Edit
                            </Link>
                        </Button>
                        <Dialog>
                            <DialogTrigger as-child>
                                <Button variant="ghost" size="icon"
                                    class="size-9 shrink-0 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                    :aria-label="`Delete ${product.name}`">
                                    <Trash2 class="size-4" />
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <Form v-bind="ProductController.destroy.form(
                                    product.id,
                                )
                                    " :options="{ preserveScroll: true }" v-slot="{ processing }">
                                    <DialogHeader class="space-y-3">
                                        <DialogTitle>Delete product?</DialogTitle>
                                        <DialogDescription>
                                            This will remove
                                            <strong>{{ product.name }}</strong>
                                            from your catalog.
                                        </DialogDescription>
                                    </DialogHeader>

                                    <DialogFooter class="gap-2">
                                        <DialogClose as-child>
                                            <Button type="button" variant="secondary">Cancel</Button>
                                        </DialogClose>
                                        <Button type="submit" variant="destructive" :disabled="processing">
                                            Delete
                                        </Button>
                                    </DialogFooter>
                                </Form>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>
            </div>

            <!-- Desktop: table -->
            <div class="hidden overflow-hidden rounded-xl border border-border bg-card shadow-sm md:block">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-260 text-sm">
                        <thead class="border-b border-border bg-muted/50 text-left">
                            <tr>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Image
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Name
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Category
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Brand
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Price
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Stock
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Sold
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Status
                                </th>
                                <th
                                    class="px-5 py-3.5 text-right text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products" :key="product.id"
                                class="border-b border-border transition-colors last:border-b-0 hover:bg-muted/30">
                                <td class="px-5 py-3.5">
                                    <div class="size-11 overflow-hidden rounded-lg border border-border bg-muted">
                                        <img v-if="product.image" :src="product.image" :alt="product.name"
                                            class="size-full object-cover" />
                                        <div v-else
                                            class="flex size-full items-center justify-center text-xs text-muted-foreground">
                                            —
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-medium text-foreground">
                                        {{ product.name }}
                                    </div>
                                    <div class="font-mono text-xs text-muted-foreground">
                                        {{ product.slug }}
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-foreground">
                                    <span>{{ product.category.name }}</span>
                                    <span v-if="product.category.is_deleted"
                                        class="ml-2 rounded-full bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground">
                                        Deleted
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-foreground">
                                    <template v-if="product.brand">
                                        <span>{{ product.brand.name }}</span>
                                        <span v-if="product.brand.is_deleted"
                                            class="ml-2 rounded-full bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground">
                                            Deleted
                                        </span>
                                    </template>
                                    <span v-else class="text-muted-foreground">—</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-medium text-foreground">
                                        {{ formatTaka(product.price) }}
                                    </div>
                                    <div v-if="product.compare_at_price"
                                        class="text-xs text-muted-foreground line-through">
                                        {{
                                            formatTaka(product.compare_at_price)
                                        }}
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                        :class="product.stock_status === 'in_stock'
                                                ? 'bg-[#00BC7D]/10 text-[#00754F]'
                                                : 'bg-destructive/10 text-destructive'
                                            ">
                                        <span class="size-1.5 rounded-full" :class="product.stock_status ===
                                                'in_stock'
                                                ? 'bg-[#00BC7D]'
                                                : 'bg-destructive'
                                            " />
                                        {{
                                            product.stock_status === 'in_stock'
                                                ? 'In stock'
                                                : 'Out of stock'
                                        }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-foreground">
                                    {{ product.sold_count }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-wrap gap-1.5">
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                            :class="product.is_active
                                                    ? 'bg-[#00BC7D]/10 text-[#00754F]'
                                                    : 'bg-muted text-muted-foreground'
                                                ">
                                            <span class="size-1.5 rounded-full" :class="product.is_active
                                                    ? 'bg-[#00BC7D]'
                                                    : 'bg-muted-foreground/50'
                                                " />
                                            {{
                                                product.is_active
                                                    ? 'Active'
                                                    : 'Inactive'
                                            }}
                                        </span>
                                        <span v-if="product.is_featured"
                                            class="rounded-full border border-border px-2.5 py-1 text-xs font-medium text-muted-foreground">
                                            Featured
                                        </span>
                                        <span v-if="product.is_best_seller"
                                            class="rounded-full border border-border px-2.5 py-1 text-xs font-medium text-muted-foreground">
                                            Best seller
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <Button as-child variant="ghost" size="icon"
                                            class="size-8 text-muted-foreground hover:bg-muted hover:text-foreground">
                                            <Link :href="show(product.id)" :aria-label="`View ${product.name}`">
                                                <Eye class="size-4" />
                                            </Link>
                                        </Button>
                                        <Button as-child variant="ghost" size="icon"
                                            class="size-8 text-muted-foreground hover:bg-muted hover:text-foreground">
                                            <Link :href="edit(product.id)" :aria-label="`Edit ${product.name}`">
                                                <Pencil class="size-4" />
                                            </Link>
                                        </Button>
                                        <Dialog>
                                            <DialogTrigger as-child>
                                                <Button variant="ghost" size="icon"
                                                    class="size-8 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                                    :aria-label="`Delete ${product.name}`">
                                                    <Trash2 class="size-4" />
                                                </Button>
                                            </DialogTrigger>
                                            <DialogContent>
                                                <Form v-bind="ProductController.destroy.form(
                                                    product.id,
                                                )
                                                    " :options="{
                                                        preserveScroll: true,
                                                    }" v-slot="{ processing }">
                                                    <DialogHeader class="space-y-3">
                                                        <DialogTitle>Delete
                                                            product?</DialogTitle>
                                                        <DialogDescription>
                                                            This will remove
                                                            <strong>{{
                                                                product.name
                                                                }}</strong>
                                                            from your catalog.
                                                        </DialogDescription>
                                                    </DialogHeader>

                                                    <DialogFooter class="gap-2">
                                                        <DialogClose as-child>
                                                            <Button type="button" variant="secondary">Cancel</Button>
                                                        </DialogClose>
                                                        <Button type="submit" variant="destructive" :disabled="processing
                                                            ">
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
            </div>
        </template>
    </div>
</template>
