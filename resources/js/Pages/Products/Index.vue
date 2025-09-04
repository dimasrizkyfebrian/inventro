<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, router } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import ProductFormModal from "@/Components/ProductFormModal.vue";

import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import InputText from "primevue/inputtext";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import { FilterMatchMode } from "@primevue/core/api";

const props = defineProps({
    products: Array,
    categories: Array,
});
const page = usePage();
const confirm = useConfirm();
const toast = useToast();
const isModalVisible = ref(false);
const editingProduct = ref(null);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(value);
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
    },
    { deep: true }
);

const openAddModal = () => {
    editingProduct.value = null;
    isModalVisible.value = true;
};
const openEditModal = (product) => {
    editingProduct.value = product;
    isModalVisible.value = true;
};
const closeModal = () => {
    isModalVisible.value = false;
    editingProduct.value = null;
};

const deleteProduct = (product) => {
    confirm.require({
        message: `Are you sure you want to delete "${product.name}"?`,
        header: "Delete Confirmation",
        icon: "pi pi-info-circle",
        rejectClass: "p-button-secondary p-button-outlined",
        acceptClass: "p-button-danger",
        accept: () => {
            router.delete(route("products.destroy", product.id), {
                preserveScroll: true,
            });
        },
    });
};
</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage Products
            </h2>
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <DataTable
                    v-model:filters="filters"
                    :value="products"
                    paginator
                    :rows="10"
                    stripedRows
                    tableStyle="min-width: 50rem"
                    dataKey="id"
                    filterDisplay="row"
                    :loading="loading"
                    :globalFilterFields="['sku', 'name']"
                >
                    <template #header>
                        <div class="flex justify-end">
                            <IconField class="mr-3">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText
                                    v-model="filters['global'].value"
                                    placeholder="Search products..."
                                />
                            </IconField>
                            <Button
                                label="Add Product"
                                icon="pi pi-plus"
                                @click="openAddModal"
                            />
                        </div>
                    </template>
                    <template #empty> No products found. </template>
                    <template #loading>
                        Loading products data. Please wait.
                    </template>
                    <Column field="id" header="ID" sortable></Column>
                    <Column field="sku" header="SKU" sortable></Column>
                    <Column field="name" header="Name" sortable></Column>
                    <Column
                        field="category.name"
                        header="Category"
                        sortable
                    ></Column>
                    <Column field="quantity" header="Qty" sortable></Column>
                    <Column field="price" header="Price" sortable>
                        <template #body="slotProps">
                            {{ formatCurrency(slotProps.data.price) }}
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="slotProps">
                            <div class="flex space-x-2">
                                <Button
                                    @click="openEditModal(slotProps.data)"
                                    icon="pi pi-pencil"
                                    class="p-button-rounded p-button-success"
                                    v-tooltip.top="'Edit'"
                                />
                                <Button
                                    @click="deleteProduct(slotProps.data)"
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
        <ProductFormModal
            :visible="isModalVisible"
            :product="editingProduct"
            :categories="categories"
            @close="closeModal"
        />
    </AuthenticatedLayout>
</template>
