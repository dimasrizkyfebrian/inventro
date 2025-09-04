<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import { useToast } from "primevue/usetoast";
import CategoryFormModal from "@/Components/CategoryFormModal.vue";
import Tooltip from "primevue/tooltip";
import { useConfirm } from "primevue/useconfirm";

const props = defineProps({
    categories: {
        type: Array,
    },
});

const page = usePage();
const toast = useToast();
const isModalVisible = ref(false);
const editingCategory = ref(null);
const confirm = useConfirm();

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

const openAddModal = () => {
    editingCategory.value = null;
    isModalVisible.value = true;
};

const openEditModal = (category) => {
    editingCategory.value = category;
    isModalVisible.value = true;
};

const closeModal = () => {
    isModalVisible.value = false;
    editingCategory.value = null;
};

const deleteCategory = (category) => {
    confirm.require({
        message: "Are you sure you want to delete this category?",
        header: "Delete Confirmation",
        icon: "pi pi-info-circle",
        rejectClass: "p-button-secondary p-button-outlined",
        acceptClass: "p-button-danger",
        accept: () => {
            router.delete(route("categories.destroy", category.id), {
                preserveScroll: true,
            });
        },
    });
};
</script>

<template>
    <Head title="Categories" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage Categories
            </h2>
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-end mb-4">
                    <Button
                        label="Add Category"
                        icon="pi pi-plus"
                        @click="openAddModal"
                    />
                </div>
                <DataTable
                    :value="categories"
                    paginator
                    :rows="10"
                    stripedRows
                    tableStyle="min-width: 50rem"
                >
                    <Column field="id" header="ID" sortable></Column>
                    <Column field="name" header="Name" sortable></Column>
                    <Column field="slug" header="Slug"></Column>
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
                                    @click="deleteCategory(slotProps.data)"
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
        <CategoryFormModal
            :visible="isModalVisible"
            :category="editingCategory"
            @close="closeModal"
        />
    </AuthenticatedLayout>
</template>
