<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

import Card from "primevue/card";
import Chart from "primevue/chart";
import DataTable from "primevue/datatable";
import Column from "primevue/column";

const props = defineProps({
    stats: Object,
    salesChartData: Object,
    topProducts: Array,
    lowStockProducts: Array,
});

const chartOptions = ref({
    maintainAspectRatio: false,
    aspectRatio: 0.9,
    scales: {
        y: {
            beginAtZero: true,
        },
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(value);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6"
                >
                    <Card>
                        <template #title>Total Sales Value</template>
                        <template #content>
                            <p class="text-2xl font-bold">
                                {{ formatCurrency(stats.totalSalesValue) }}
                            </p>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Total Products</template>
                        <template #content>
                            <p class="text-2xl font-bold">
                                {{ stats.totalProducts }}
                            </p>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Total Sales Orders</template>
                        <template #content>
                            <p class="text-2xl font-bold">
                                {{ stats.totalTransactions }}
                            </p>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Low Stock Items</template>
                        <template #content>
                            <p class="text-2xl font-bold text-orange-500">
                                {{ stats.lowStockProductsCount }}
                            </p>
                        </template>
                    </Card>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div
                        class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                    >
                        <h3 class="font-bold mb-4">
                            Sales Trend (Last 30 Days)
                        </h3>
                        <Chart
                            type="line"
                            :data="salesChartData"
                            :options="chartOptions"
                        />
                    </div>

                    <div class="flex flex-col gap-6">
                        <div
                            class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                        >
                            <h3 class="font-bold mb-4">
                                Top 5 Best-Selling Products
                            </h3>
                            <DataTable :value="topProducts" size="small">
                                <Column
                                    field="product.name"
                                    header="Product"
                                ></Column>
                                <Column
                                    field="total_sold"
                                    header="Sold"
                                ></Column>
                            </DataTable>
                        </div>
                        <div
                            class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                        >
                            <h3 class="font-bold mb-4">
                                Products with Low Stock
                            </h3>
                            <DataTable :value="lowStockProducts" size="small">
                                <Column field="name" header="Product"></Column>
                                <Column
                                    field="quantity"
                                    header="Qty Left"
                                ></Column>
                            </DataTable>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
