<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { useToast } from "primevue/usetoast";
import StockAdjustmentFormModal from "@/Components/StockAdjustmentFormModal.vue";

import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import Tag from "primevue/tag";

const props = defineProps({
    adjustments: Array,
    products: Array,
});

const isModalVisible = ref(false);
const page = usePage();
const toast = useToast();

watch(
    () => page.props.flash,
    (flash) => {
        if (flash && flash.success) {
            toast.add({
                severity: "success",
                summary: "Success",
                detail: flash.success,
                life: 3000,
            });
        }
        if (flash && flash.error) {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: flash.error,
                life: 3000,
            });
        }
    },
    { deep: true }
);

const getAdjustmentTypeSeverity = (type) => {
    return type === "addition" ? "success" : "danger";
};
</script>

<template>
    <Head title="Stock Adjustments" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Stock Adjustments
            </h2>
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-end mb-4">
                    <Button
                        label="New Adjustment"
                        icon="pi pi-plus"
                        @click="isModalVisible = true"
                    />
                </div>

                <DataTable
                    :value="adjustments"
                    dataKey="id"
                    paginator
                    :rows="10"
                    stripedRows
                >
                    <Column field="created_at" header="Date" sortable>
                        <template #body="slotProps">
                            {{
                                new Date(
                                    slotProps.data.created_at
                                ).toLocaleDateString()
                            }}
                        </template>
                    </Column>
                    <Column
                        field="product.name"
                        header="Product"
                        sortable
                    ></Column>
                    <Column field="type" header="Type" sortable>
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.type"
                                :severity="
                                    getAdjustmentTypeSeverity(
                                        slotProps.data.type
                                    )
                                "
                            />
                        </template>
                    </Column>
                    <Column field="quantity" header="Quantity"></Column>
                    <Column field="reason" header="Reason"></Column>
                    <Column field="user.name" header="Adjusted By"></Column>
                </DataTable>
            </div>
        </div>

        <StockAdjustmentFormModal
            :visible="isModalVisible"
            :products="products"
            @close="isModalVisible = false"
        />
    </AuthenticatedLayout>
</template>
