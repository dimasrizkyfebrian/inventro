<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Button from "primevue/button";

const props = defineProps({
    product: Object,
});
</script>
<template>
    <Head :title="`Product - ${product.name}`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Product Details
                </h2>
                <Link :href="route('products.index')">
                    <Button
                        label="Back to List"
                        icon="pi pi-arrow-left"
                        severity="secondary"
                        outlined
                    />
                </Link>
            </div>
        </template>

        <div
            class="p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg grid grid-cols-3 gap-6"
        >
            <div class="col-span-2">
                <h3 class="text-2xl font-bold mb-4">{{ product.name }}</h3>
                <div class="space-y-2">
                    <p><strong>SKU:</strong> {{ product.sku }}</p>
                    <p>
                        <strong>Category:</strong> {{ product.category.name }}
                    </p>
                    <p>
                        <strong>Current Stock:</strong>
                        {{ product.quantity }} units
                    </p>
                </div>
            </div>
            <div class="col-span-1 text-center">
                <h4 class="font-semibold mb-2">Product QR Code</h4>
                <img
                    :src="route('products.qrcode', product.id)"
                    alt="QR Code"
                    class="mx-auto border p-1"
                />
                <p class="text-sm text-gray-500 mt-2">
                    Scan for SKU: {{ product.sku }}
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
