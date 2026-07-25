<script setup lang="ts">
import type { AdminOrder } from '@/types/admin';

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

import { Separator } from '@/components/ui/separator';

import { Mail, Phone, User } from '@lucide/vue';

defineProps<{
    order: AdminOrder;
}>();
</script>

<template>
    <Card>
        <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-base font-semibold">
                <User class="size-4 text-primary" />

                Customer
            </CardTitle>
        </CardHeader>

        <Separator />

        <CardContent class="space-y-3 pt-4 text-sm">
            <div>
                <p class="font-medium text-foreground">
                    {{ order.customer_name }}
                </p>

                <p class="text-xs text-muted-foreground">
                    {{
                        order.customer
                            ? 'Registered Customer'
                            : 'Guest Checkout'
                    }}
                </p>
            </div>

            <div class="flex items-center gap-2 text-muted-foreground">
                <Phone class="size-3.5 shrink-0" />

                <span>
                    {{ order.phone }}
                </span>
            </div>

            <div class="flex items-center gap-2 text-muted-foreground">
                <Mail class="size-3.5 shrink-0" />

                <span class="truncate">
                    {{ order.email }}
                </span>
            </div>
        </CardContent>
    </Card>
</template>
