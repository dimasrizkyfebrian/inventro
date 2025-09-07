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
import DatePicker from "primevue/datepicker";

defineProps({ salesOrders: Array });

const page = usePage();
const toast = useToast();
const confirm = useConfirm();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const reportDates = ref({
    start_date: null,
    end_date: null,
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

const deleteSalesOrder = (salesOrder) => {
    confirm.require({
        message: `Are you sure you want to delete SO Number: ${salesOrder.so_number}? This action will revert the product stock.`,
        header: "Delete Confirmation",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        accept: () => {
            router.delete(route("sales-orders.destroy", salesOrder.id), {
                preserveScroll: true,
            });
        },
    });
};

const generateReport = () => {
    if (!reportDates.value.start_date || !reportDates.value.end_date) {
        alert("Please select both start and end dates.");
        return;
    }

    const startDate = new Date(reportDates.value.start_date)
        .toISOString()
        .slice(0, 10);
    const endDate = new Date(reportDates.value.end_date)
        .toISOString()
        .slice(0, 10);

    window.open(
        route("reports.sales", { start_date: startDate, end_date: endDate }),
        "_blank"
    );
};
</script>

<template>
    <Head title="Sales Orders" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sales Orders
            </h2>
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div
                    class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4"
                >
                    <div class="flex items-center gap-2 border p-2 rounded-md">
                        <DatePicker
                            v-model="reportDates.start_date"
                            placeholder="Start Date"
                            dateFormat="yy-mm-dd"
                        />
                        <span>to</span>
                        <DatePicker
                            v-model="reportDates.end_date"
                            placeholder="End Date"
                            dateFormat="yy-mm-dd"
                        />
                        <Button
                            label="Generate Report"
                            icon="pi pi-print"
                            @click="generateReport"
                        />
                    </div>
                </div>
                <DataTable
                    v-model:filters="filters"
                    :value="salesOrders"
                    paginator
                    :rows="10"
                    stripedRows
                    tableStyle="min-width: 50rem"
                    dataKey="id"
                    filterDisplay="row"
                    :loading="loading"
                    :globalFilterFields="['so_number']"
                >
                    <template #header>
                        <div class="flex justify-end">
                            <IconField class="mr-3">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText
                                    v-model="filters['global'].value"
                                    placeholder="Search sales orders"
                                />
                            </IconField>
                            <Link :href="route('sales-orders.create')">
                                <Button
                                    label="New Sales Order"
                                    icon="pi pi-plus"
                                />
                            </Link>
                        </div>
                    </template>
                    <template #empty> No purchase orders found. </template>
                    <template #loading>
                        Loading purchase orders data. Please wait.
                    </template>
                    <Column field="so_number" header="SO Number" sortable>
                        <template #body="slotProps">
                            <Link
                                :href="
                                    route(
                                        'sales-orders.show',
                                        slotProps.data.id
                                    )
                                "
                                class="text-blue-600 hover:underline font-semibold"
                            >
                                {{ slotProps.data.so_number }}
                            </Link>
                        </template>
                    </Column>
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
                            <Button
                                @click="deleteSalesOrder(slotProps.data)"
                                icon="pi pi-trash"
                                class="p-button-rounded p-button-danger"
                                v-tooltip.top="'Delete'"
                            />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
