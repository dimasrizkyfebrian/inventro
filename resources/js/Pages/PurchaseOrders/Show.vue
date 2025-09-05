<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Button from "primevue/button";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";

const props = defineProps({
    purchaseOrder: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(value);
};

const totalAmount = props.purchaseOrder.items.reduce(
    (acc, item) => acc + item.quantity * item.cost,
    0
);
</script>

<template>
    <Head :title="`PO - ${purchaseOrder.po_number}`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Purchase Order Details: {{ purchaseOrder.po_number }}
                </h2>
                <Link :href="route('purchase-orders.index')">
                    <Button
                        label="Back to List"
                        icon="pi pi-arrow-left"
                        severity="secondary"
                        outlined
                    />
                </Link>
            </div>
        </template>

        <div class="p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div
                class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 border-b pb-4"
            >
                <div>
                    <label class="block text-sm font-medium text-gray-500"
                        >PO Number</label
                    >
                    <p class="mt-1 text-lg font-semibold">
                        {{ purchaseOrder.po_number }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500"
                        >Supplier</label
                    >
                    <p class="mt-1 text-lg">
                        {{ purchaseOrder.supplier.name }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500"
                        >Order Date</label
                    >
                    <p class="mt-1 text-lg">{{ purchaseOrder.order_date }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500"
                        >Status</label
                    >
                    <Tag value="Completed" severity="success" class="mt-1" />
                </div>
            </div>

            <h3 class="font-bold mb-2">Items Included</h3>
            <DataTable
                :value="purchaseOrder.items"
                tableStyle="min-width: 50rem"
            >
                <Column field="product.sku" header="SKU"></Column>
                <Column field="product.name" header="Product Name"></Column>
                <Column field="quantity" header="Quantity"></Column>
                <Column field="cost" header="Unit Cost">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.cost) }}
                    </template>
                </Column>
                <Column header="Subtotal">
                    <template #body="slotProps">
                        {{
                            formatCurrency(
                                slotProps.data.quantity * slotProps.data.cost
                            )
                        }}
                    </template>
                </Column>
            </DataTable>

            <div class="flex justify-end mt-4">
                <div class="text-right">
                    <p class="text-gray-500">Total Amount</p>
                    <p class="text-2xl font-bold">
                        {{ formatCurrency(totalAmount) }}
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
