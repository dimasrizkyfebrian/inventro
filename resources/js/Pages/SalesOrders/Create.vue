<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";

import Button from "primevue/button";
import DatePicker from "primevue/datepicker";
import InputNumber from "primevue/inputnumber";
import AutoComplete from "primevue/autocomplete";

const props = defineProps({
    products: Array,
});

const icondisplay = ref();

const form = useForm({
    order_date: new Date(),
    notes: "",
    items: [],
});

const filteredProducts = ref([]);

const addProductItem = () => {
    form.items.push({
        product_id: null,
        product_name: "",
        quantity: 1,
        price: 0,
        available_stock: 0,
    });
};

const removeProductItem = (index) => {
    form.items.splice(index, 1);
};

const onProductSelect = (event, item) => {
    const selectedProduct = event.value;
    item.product_id = selectedProduct.id;
    item.price = selectedProduct.price ?? 0;
    item.available_stock = selectedProduct.quantity;
    item.product_name = selectedProduct.name;
};

const searchProduct = (event) => {
    if (!event.query.trim().length) {
        filteredProducts.value = [...props.products];
    } else {
        filteredProducts.value = props.products.filter((product) => {
            const query = event.query.toLowerCase();
            return (
                product.name.toLowerCase().includes(query) ||
                product.sku.toLowerCase().includes(query)
            );
        });
    }
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        items: data.items.map((item) => ({
            product_id: item.product_id,
            quantity: item.quantity,
            price: item.price,
        })),
    })).post(route("sales-orders.store"));
};
</script>

<template>
    <Head title="Create Sales Order" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Create Sales Order
                </h2>
                <Link :href="route('sales-orders.index')">
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
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label for="order_date">Order Date</label>
                        <DatePicker
                            v-model="icondisplay"
                            showIcon
                            fluid
                            iconDisplay="input"
                        />
                    </div>
                </div>

                <h3 class="font-bold mb-2">Products</h3>
                <div class="border rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="p-3 text-left w-2/5">Product</th>
                                    <th class="p-3 text-left w-1/5">
                                        Quantity
                                    </th>
                                    <th class="p-3 text-left w-1/5">Price</th>
                                    <th class="p-3 text-left w-1/5">
                                        Subtotal
                                    </th>
                                    <th class="p-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(item, index) in form.items"
                                    :key="index"
                                    class="border-t"
                                >
                                    <td class="p-2">
                                        <AutoComplete
                                            v-model="item.product_name"
                                            :suggestions="filteredProducts"
                                            @complete="searchProduct"
                                            @item-select="
                                                onProductSelect($event, item)
                                            "
                                            field="name"
                                            placeholder="Search by name or SKU"
                                            class="w-full"
                                        >
                                            <template #option="slotProps">
                                                <div>
                                                    {{ slotProps.option.name }}
                                                    (Stock:
                                                    {{
                                                        slotProps.option
                                                            .quantity
                                                    }})
                                                </div>
                                            </template>
                                        </AutoComplete>
                                    </td>
                                    <td class="p-2">
                                        <InputNumber
                                            v-model="item.quantity"
                                            :min="1"
                                            :max="item.available_stock"
                                        />
                                    </td>
                                    <td class="p-2">
                                        <InputNumber
                                            v-model="item.price"
                                            mode="currency"
                                            currency="USD"
                                            locale="en-US"
                                        />
                                    </td>
                                    <td class="p-2 font-semibold">
                                        {{
                                            (
                                                (item.quantity || 0) *
                                                (item.price || 0)
                                            ).toLocaleString("en-US", {
                                                style: "currency",
                                                currency: "USD",
                                            })
                                        }}
                                    </td>
                                    <td class="p-2 text-center">
                                        <Button
                                            @click="removeProductItem(index)"
                                            icon="pi pi-trash"
                                            severity="danger"
                                            text
                                            rounded
                                        />
                                    </td>
                                </tr>
                                <tr v-if="form.items.length === 0">
                                    <td
                                        colspan="5"
                                        class="text-center p-4 text-gray-500"
                                    >
                                        Click "Add Product" to begin.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-t">
                        <Button
                            @click="addProductItem"
                            type="button"
                            label="Add Product"
                            icon="pi pi-plus"
                            severity="secondary"
                        />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <Button
                        type="submit"
                        label="Save Sales Order"
                        :loading="form.processing"
                    />
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
