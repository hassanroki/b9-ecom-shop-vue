<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ShopCheckoutSummary from '@/components/shop/ShopCheckoutSummary.vue';
import ShopPageBreadcrumb from '@/components/shop/ShopPageBreadcrumb.vue';
import { useShopCart } from '@/composables/shop/useShopCart';
import { useShopUi } from '@/composables/shop/useShopUi';
import type { ShopCheckoutConfig } from '@/types/shop';
import shop from '@/routes/shop';

type PaymentMethod = 'cod' | 'sslcommerz' | 'stripe';

type AppliedCoupon = {
    code: string;
    discount: number;
};

const { districts, deliveryCharges, appliedCoupon, stripeExchangeRate = 0.0084 } = defineProps<{
    districts: string[];
    deliveryCharges: ShopCheckoutConfig;
    appliedCoupon?: AppliedCoupon | null;
    stripeExchangeRate?: number;
}>();

const { cart, cartSubtotal, updateQty, removeItem } = useShopCart();
const { showToast } = useShopUi();

const form = useForm({
    customer_name: '',
    phone: '',
    email: '',
    district: '',
    area: '',
    address: '',
    notes: '',
    payment_method: 'cod' as PaymentMethod,
    coupon_code: appliedCoupon?.code ?? '',
});

// --- Coupon state ---
const couponInput = ref('');
const coupon = ref<AppliedCoupon | null>(appliedCoupon ?? null);
const couponError = ref('');
const couponApplying = ref(false);

const deliveryCharge = computed(() => {
    if (cart.value.length === 0) {
        return 0;
    }

    if (!form.district) {
        return deliveryCharges.outsideDhaka;
    }

    return form.district === deliveryCharges.dhakaDistrict
        ? deliveryCharges.insideDhaka
        : deliveryCharges.outsideDhaka;
});

const deliveryNote = computed(() => {
    if (cart.value.length === 0 || !form.district) {
        return '';
    }

    return form.district === deliveryCharges.dhakaDistrict
        ? '(Inside Dhaka)'
        : '(Outside Dhaka)';
});

const couponDiscount = computed(() => coupon.value?.discount ?? 0);

const grandTotal = computed(() =>
    Math.max(
        cartSubtotal.value + deliveryCharge.value - couponDiscount.value,
        0,
    ),
);

const submitLabel = computed(() => {
    if (form.payment_method === 'sslcommerz') return 'Proceed to Payment';
    if (form.payment_method === 'stripe') return 'Pay with Stripe';
    return 'Place Order';
});

function handleIncrement(productId: number): void {
    const item = cart.value.find((i) => i.productId === productId);
    if (item) {
        updateQty(productId, item.qty + 1);
    }
}

function handleDecrement(productId: number): void {
    const item = cart.value.find((i) => i.productId === productId);
    if (item && item.qty > 1) {
        updateQty(productId, item.qty - 1);
    }
}

function handleRemove(productId: number): void {
    const item = cart.value.find((i) => i.productId === productId);
    removeItem(productId);
    if (item) {
        showToast(`Removed: ${item.name}`);
    }
}

async function applyCoupon(): Promise<void> {
    const code = couponInput.value.trim();

    if (!code) {
        couponError.value = 'Please enter a coupon code.';
        return;
    }

    couponApplying.value = true;
    couponError.value = '';

    try {
        const response = await fetch(shop.checkout.coupon.apply.url(), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN':
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content') ?? '',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                code,
                email: form.email || null,
                phone: form.phone || null,
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            couponError.value = data.message ?? 'Unable to apply this coupon.';
            return;
        }

        coupon.value = {
            code: data.code,
            discount: data.discount,
        };
        form.coupon_code = data.code;
        couponInput.value = '';
        showToast(data.message ?? 'Coupon applied.');
    } catch {
        couponError.value = 'Something went wrong. Please try again.';
    } finally {
        couponApplying.value = false;
    }
}

async function removeCoupon(): Promise<void> {
    try {
        await fetch(shop.checkout.coupon.remove.url(), {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN':
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content') ?? '',
            },
            credentials: 'same-origin',
        });
    } finally {
        coupon.value = null;
        form.coupon_code = '';
        couponError.value = '';
        showToast('Coupon removed.');
    }
}

function handleSubmit(): void {
    if (cart.value.length === 0) {
        showToast('Your cart is empty');
        return;
    }

    form.post(shop.checkout.store.url(), {
        preserveScroll: true,
        onError: (errors) => {
            if (errors.coupon_code) {
                couponError.value = errors.coupon_code;
                coupon.value = null;
            }
            showToast('Please complete the highlighted fields');
        },
    });
}
</script>

<template>
    <Head title="Checkout">
        <meta
            name="description"
            content="Secure checkout with Cash on Delivery or SSLCommerz online payment. Delivery across Bangladesh."
        />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin="anonymous"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div class="bg-gray-50 py-6 md:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <ShopPageBreadcrumb
                :items="[
                    { label: 'Cart', href: shop.cart.url() },
                    { label: 'Checkout' },
                ]"
            />

            <form
                novalidate
                class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:gap-8"
                @submit.prevent="handleSubmit"
            >
                <div class="space-y-6 lg:col-span-2">
                    <section
                        class="rounded-xl border border-gray-200 bg-white p-5 md:p-6"
                    >
                        <h2 class="text-lg font-semibold text-gray-900">
                            Shipping Details
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Where should we deliver your order?
                        </p>

                        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label
                                    for="customer_name"
                                    class="mb-1.5 block text-sm font-medium text-gray-700"
                                >
                                    Full name
                                    <span class="text-red-600">*</span>
                                </label>
                                <input
                                    id="customer_name"
                                    v-model="form.customer_name"
                                    name="customer_name"
                                    type="text"
                                    required
                                    autocomplete="name"
                                    class="w-full rounded-lg border px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none"
                                    :class="
                                        form.errors.customer_name
                                            ? 'border-red-500 focus:ring-red-500'
                                            : 'border-gray-300 focus:border-shop-primary-600'
                                    "
                                    placeholder="e.g. Rina Akter"
                                />
                                <p
                                    v-show="form.errors.customer_name"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.customer_name }}
                                </p>
                            </div>
                            <div>
                                <label
                                    for="phone"
                                    class="mb-1.5 block text-sm font-medium text-gray-700"
                                >
                                    Phone
                                    <span class="text-red-600">*</span>
                                </label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    name="phone"
                                    type="tel"
                                    required
                                    autocomplete="tel"
                                    inputmode="numeric"
                                    class="w-full rounded-lg border px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none"
                                    :class="
                                        form.errors.phone
                                            ? 'border-red-500 focus:ring-red-500'
                                            : 'border-gray-300 focus:border-shop-primary-600'
                                    "
                                    placeholder="01XXXXXXXXX"
                                />
                                <p
                                    v-show="form.errors.phone"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.phone }}
                                </p>
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    for="email"
                                    class="mb-1.5 block text-sm font-medium text-gray-700"
                                >
                                    Email
                                    <span class="text-red-600">*</span>
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    name="email"
                                    type="email"
                                    required
                                    autocomplete="email"
                                    class="w-full rounded-lg border px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none"
                                    :class="
                                        form.errors.email
                                            ? 'border-red-500 focus:ring-red-500'
                                            : 'border-gray-300 focus:border-shop-primary-600'
                                    "
                                    placeholder="you@example.com"
                                />
                                <p
                                    v-show="form.errors.email"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>
                            <div>
                                <label
                                    for="district"
                                    class="mb-1.5 block text-sm font-medium text-gray-700"
                                >
                                    District
                                    <span class="text-red-600">*</span>
                                </label>
                                <select
                                    id="district"
                                    v-model="form.district"
                                    name="district"
                                    required
                                    class="w-full rounded-lg border bg-white px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none"
                                    :class="
                                        form.errors.district
                                            ? 'border-red-500 focus:ring-red-500'
                                            : 'border-gray-300 focus:border-shop-primary-600'
                                    "
                                >
                                    <option value="">Select district</option>
                                    <option
                                        v-for="district in districts"
                                        :key="district"
                                        :value="district"
                                    >
                                        {{ district }}
                                    </option>
                                </select>
                                <p
                                    v-show="form.errors.district"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.district }}
                                </p>
                            </div>
                            <div>
                                <label
                                    for="area"
                                    class="mb-1.5 block text-sm font-medium text-gray-700"
                                >
                                    Area / Thana
                                    <span class="text-red-600">*</span>
                                </label>
                                <input
                                    id="area"
                                    v-model="form.area"
                                    name="area"
                                    type="text"
                                    required
                                    class="w-full rounded-lg border px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none"
                                    :class="
                                        form.errors.area
                                            ? 'border-red-500 focus:ring-red-500'
                                            : 'border-gray-300 focus:border-shop-primary-600'
                                    "
                                    placeholder="e.g. Dhanmondi"
                                />
                                <p
                                    v-show="form.errors.area"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.area }}
                                </p>
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    for="address"
                                    class="mb-1.5 block text-sm font-medium text-gray-700"
                                >
                                    Full address
                                    <span class="text-red-600">*</span>
                                </label>
                                <textarea
                                    id="address"
                                    v-model="form.address"
                                    name="address"
                                    rows="2"
                                    required
                                    class="w-full rounded-lg border px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none"
                                    :class="
                                        form.errors.address
                                            ? 'border-red-500 focus:ring-red-500'
                                            : 'border-gray-300 focus:border-shop-primary-600'
                                    "
                                    placeholder="House, road, and any landmark"
                                />
                                <p
                                    v-show="form.errors.address"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.address }}
                                </p>
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    for="notes"
                                    class="mb-1.5 block text-sm font-medium text-gray-700"
                                >
                                    Order notes
                                    <span class="text-gray-400"
                                        >(optional)</span
                                    >
                                </label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    name="notes"
                                    rows="2"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 focus:border-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none"
                                    placeholder="Delivery instructions, preferred time, etc."
                                />
                            </div>
                        </div>
                    </section>

                    <section
                        class="rounded-xl border border-gray-200 bg-white p-5 md:p-6"
                    >
                        <h2 class="text-lg font-semibold text-gray-900">
                            Coupon Code
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Have a discount code? Apply it below.
                        </p>

                        <div
                            v-if="!coupon"
                            class="mt-4 flex flex-col gap-2 sm:flex-row"
                        >
                            <input
                                v-model="couponInput"
                                type="text"
                                class="w-full flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 uppercase focus:border-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none"
                                placeholder="e.g. SUMMER20"
                                :disabled="couponApplying"
                                @keydown.enter.prevent="applyCoupon"
                            />
                            <button
                                type="button"
                                class="shrink-0 rounded-lg bg-shop-primary-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-shop-primary-700 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="couponApplying"
                                @click="applyCoupon"
                            >
                                {{ couponApplying ? 'Applying...' : 'Apply' }}
                            </button>
                        </div>

                        <div
                            v-else
                            class="mt-4 flex items-center justify-between rounded-lg border border-green-200 bg-green-50 px-4 py-3"
                        >
                            <div>
                                <p class="text-sm font-semibold text-green-800">
                                    {{ coupon.code }} applied
                                </p>
                                <p class="text-sm text-green-700">
                                    You saved ৳{{ coupon.discount.toFixed(2) }}
                                </p>
                            </div>
                            <button
                                type="button"
                                class="text-sm font-medium text-red-600 hover:underline"
                                @click="removeCoupon"
                            >
                                Remove
                            </button>
                        </div>

                        <p
                            v-show="couponError"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ couponError }}
                        </p>
                    </section>

                    <section
                        class="rounded-xl border border-gray-200 bg-white p-5 md:p-6"
                    >
                        <h2 class="text-lg font-semibold text-gray-900">
                            Payment Method
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Choose how you would like to pay for your order.
                        </p>

                        <div class="mt-5 space-y-3">
                            <label
                                class="flex cursor-pointer items-start gap-3 rounded-xl border-2 p-4 transition"
                                :class="
                                    form.payment_method === 'cod'
                                        ? 'border-shop-primary-600 bg-shop-primary-50'
                                        : 'border-gray-200 hover:border-gray-300'
                                "
                            >
                                <input
                                    v-model="form.payment_method"
                                    type="radio"
                                    name="payment_method"
                                    value="cod"
                                    class="mt-1 h-4 w-4 border-gray-300 text-shop-primary-600 focus:ring-shop-primary-600"
                                />
                                <svg
                                    class="mt-0.5 h-6 w-6 shrink-0 text-shop-primary-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h2m2-6h10a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6a2 2 0 012-2zm7 5a2 2 0 11-4 0 2 2 0 014 0z"
                                    />
                                </svg>
                                <div>
                                    <p
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        Cash on Delivery
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Pay the delivery agent in cash when you
                                        receive your order.
                                    </p>
                                </div>
                            </label>

                            <label
                                class="flex cursor-pointer items-start gap-3 rounded-xl border-2 p-4 transition"
                                :class="
                                    form.payment_method === 'sslcommerz'
                                        ? 'border-shop-primary-600 bg-shop-primary-50'
                                        : 'border-gray-200 hover:border-gray-300'
                                "
                            >
                                <input
                                    v-model="form.payment_method"
                                    type="radio"
                                    name="payment_method"
                                    value="sslcommerz"
                                    class="mt-1 h-4 w-4 border-gray-300 text-shop-primary-600 focus:ring-shop-primary-600"
                                />
                                <svg
                                    class="mt-0.5 h-6 w-6 shrink-0 text-shop-primary-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                    />
                                </svg>
                                <div>
                                    <p
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        Pay Online (SSLCommerz)
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Pay securely with bKash, Nagad, cards,
                                        or mobile banking via SSLCommerz.
                                    </p>
                                </div>
                            </label>

                            <!-- ===== Stripe ===== -->
                            <label
                                class="flex cursor-pointer items-start gap-3 rounded-xl border-2 p-4 transition"
                                :class="
                                    form.payment_method === 'stripe'
                                        ? 'border-shop-primary-600 bg-shop-primary-50'
                                        : 'border-gray-200 hover:border-gray-300'
                                "
                            >
                                <input
                                    v-model="form.payment_method"
                                    type="radio"
                                    name="payment_method"
                                    value="stripe"
                                    class="mt-1 h-4 w-4 border-gray-300 text-shop-primary-600 focus:ring-shop-primary-600"
                                />
                                <!-- Stripe card icon -->
                                <svg
                                    class="mt-0.5 h-6 w-6 shrink-0 text-[#635BFF]"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Zm2 0v2h16V6H4Zm0 4v2h4v-2H4Zm0 4v2h4v-2H4Zm6-4v2h2v-2h-2Zm4 0v2h2v-2h-2Zm-4 4v2h2v-2h-2Zm4 0v2h4v-2h-4Z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">
                                        Pay with Stripe
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Pay securely with Visa, Mastercard, or
                                        any major credit / debit card via Stripe.
                                    </p>
                                    <div
                                        v-if="form.payment_method === 'stripe'"
                                        class="mt-2 text-xs font-semibold text-[#635BFF] bg-[#635BFF]/5 border border-[#635BFF]/10 rounded-lg px-2.5 py-1.5 inline-flex items-center gap-1.5 transition-all duration-300"
                                    >
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#635BFF] opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#635BFF]"></span>
                                        </span>
                                        Amount to pay is approx. ${{ (grandTotal * stripeExchangeRate).toFixed(2) }} USD (BDT {{ grandTotal.toLocaleString() }})
                                    </div>
                                </div>
                            </label>
                        </div>

                        <p
                            v-show="form.errors.payment_method"
                            class="mt-3 text-sm text-red-600"
                        >
                            {{ form.errors.payment_method }}
                        </p>
                    </section>
                </div>

                <div class="lg:col-span-1">
                    <div class="lg:sticky lg:top-24">
                        <ShopCheckoutSummary
                            :items="cart"
                            :subtotal="cartSubtotal"
                            :delivery-charge="deliveryCharge"
                            :delivery-note="deliveryNote"
                            :discount="couponDiscount"
                            :coupon-code="coupon?.code ?? null"
                            :grand-total="grandTotal"
                            :is-empty="cart.length === 0"
                            :processing="form.processing"
                            :submit-label="submitLabel"
                            :payment-method="form.payment_method"
                            :stripe-exchange-rate="stripeExchangeRate"
                            @increment="handleIncrement"
                            @decrement="handleDecrement"
                            @remove="handleRemove"
                        />
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
