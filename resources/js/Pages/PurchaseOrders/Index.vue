<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import { FilterMatchMode } from "@primevue/core/api";

import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import Tag from "primevue/tag";

const page = usePage();
const toast = useToast();
const confirm = useConfirm();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

watch(
    () => page.props.flash.success,
    (message) => {
        if (message) {
            toast.add({
                severity: "success",
                summary: "Success",
                detail: message,
                life: 3000,
            });
        }
    }
);

const props = defineProps({
    purchaseOrders: Array,
});

const getStatusSeverity = (status) => {
    switch (status) {
        case "completed":
            return "success";
        case "pending":
            return "warning";
        case "cancelled":
            return "danger";
        default:
            return "info";
    }
};

const deletePurchaseOrder = (purchaseOrder) => {
    confirm.require({
        message: `Are you sure you want to delete PO Number: ${purchaseOrder.po_number}? This action will also revert the product stock.`,
        header: "Delete Confirmation",
        icon: "pi pi-info-circle",
        rejectClass: "p-button-secondary p-button-outlined",
        acceptClass: "p-button-danger",
        accept: () => {
            router.delete(route("purchase-orders.destroy", purchaseOrder.id), {
                preserveScroll: true,
            });
        },
    });
};
</script>

<template>
    <Head title="Purchase Orders" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Purchase Orders
            </h2>
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <DataTable
                    v-model:filters="filters"
                    :value="purchaseOrders"
                    paginator
                    :rows="10"
                    stripedRows
                    tableStyle="min-width: 50rem"
                    dataKey="id"
                    filterDisplay="row"
                    :loading="loading"
                    :globalFilterFields="['po_number']"
                >
                    <template #header>
                        <div class="flex justify-end">
                            <IconField class="mr-3">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText
                                    v-model="filters['global'].value"
                                    placeholder="Search purchase orders"
                                />
                            </IconField>
                            <Link :href="route('purchase-orders.create')">
                                <Button
                                    label="New Purchase Order"
                                    icon="pi pi-plus"
                                />
                            </Link>
                        </div>
                    </template>
                    <template #empty> No purchase orders found. </template>
                    <template #loading>
                        Loading purchase orders data. Please wait.
                    </template>
                    <Column field="po_number" header="PO Number" sortable>
                        <template #body="slotProps">
                            <Link
                                :href="
                                    route(
                                        'purchase-orders.show',
                                        slotProps.data.id
                                    )
                                "
                                class="text-blue-600 hover:underline font-semibold"
                            >
                                {{ slotProps.data.po_number }}
                            </Link>
                        </template>
                    </Column>
                    <Column
                        field="supplier.name"
                        header="Supplier"
                        sortable
                    ></Column>
                    <Column
                        field="order_date"
                        header="Order Date"
                        sortable
                    ></Column>
                    <Column field="status" header="Status">
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.status"
                                :severity="
                                    getStatusSeverity(slotProps.data.status)
                                "
                            />
                        </template>
                    </Column>
                    <Column field="user.name" header="Created By"></Column>
                    <Column header="Actions">
                        <template #body="slotProps">
                            <div class="flex space-x-2">
                                <Link
                                    :href="
                                        route(
                                            'purchase-orders.edit',
                                            slotProps.data.id
                                        )
                                    "
                                >
                                    <Button
                                        icon="pi pi-pencil"
                                        class="p-button-rounded p-button-success"
                                        v-tooltip.top="'Edit'"
                                    />
                                </Link>
                                <Button
                                    @click="deletePurchaseOrder(slotProps.data)"
                                    icon="pi pi-trash"
                                    class="p-button-rounded p-button-danger"
                                    v-tooltip.top="'Delete'"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
