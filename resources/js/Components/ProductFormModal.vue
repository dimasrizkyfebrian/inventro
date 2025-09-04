<script setup>
import { useForm } from "@inertiajs/vue3";
import { watch, computed } from "vue";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import Textarea from "primevue/textarea";
import Dropdown from "primevue/dropdown";
import Button from "primevue/button";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    visible: Boolean,
    product: { type: Object, default: null },
    categories: { type: Array, required: true },
});
const emit = defineEmits(["close"]);

const isEditMode = computed(() => !!props.product);

const form = useForm({
    name: "",
    sku: "",
    description: "",
    quantity: 0,
    price: 0,
    cost: null,
    category_id: null,
});

watch(
    () => props.product,
    (newVal) => {
        if (newVal) {
            form.name = newVal.name;
            form.sku = newVal.sku;
            form.description = newVal.description;
            form.quantity = newVal.quantity;
            form.price = newVal.price;
            form.cost = newVal.cost;
            form.category_id = newVal.category_id;
        } else {
            form.reset();
        }
    }
);

const submit = () => {
    if (isEditMode.value) {
        form.put(route("products.update", props.product.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("products.store"), { onSuccess: () => closeModal() });
    }
};

const closeModal = () => {
    form.reset();
    form.clearErrors();
    emit("close");
};
</script>

<template>
    <Dialog
        :visible="visible"
        modal
        :header="isEditMode ? 'Edit Product' : 'Add New Product'"
        :style="{ width: '40rem' }"
        @update:visible="closeModal"
    >
        <form @submit.prevent="submit" class="p-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label for="name">Name</label>
                    <InputText
                        id="name"
                        v-model="form.name"
                        required
                        autofocus
                        :invalid="form.errors.name"
                    />
                    <InputError :message="form.errors.name" />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="sku">SKU</label>
                    <InputText
                        id="sku"
                        v-model="form.sku"
                        required
                        :invalid="form.errors.sku"
                    />
                    <InputError :message="form.errors.sku" />
                </div>
                <div class="flex flex-col gap-2 col-span-2">
                    <label for="category_id">Category</label>
                    <Dropdown
                        id="category_id"
                        v-model="form.category_id"
                        :options="categories"
                        optionLabel="name"
                        optionValue="id"
                        placeholder="Select a Category"
                        class="w-full"
                        :invalid="form.errors.category_id"
                    />
                    <InputError :message="form.errors.category_id" />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="quantity">Quantity</label>
                    <InputNumber
                        id="quantity"
                        v-model="form.quantity"
                        :invalid="form.errors.quantity"
                    />
                    <InputError :message="form.errors.quantity" />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="price">Price</label>
                    <InputNumber
                        id="price"
                        v-model="form.price"
                        mode="currency"
                        currency="USD"
                        locale="en-US"
                        :invalid="form.errors.price"
                    />
                    <InputError :message="form.errors.price" />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="cost">Cost (Optional)</label>
                    <InputNumber
                        id="cost"
                        v-model="form.cost"
                        mode="currency"
                        currency="USD"
                        locale="en-US"
                        :invalid="form.errors.cost"
                    />
                    <InputError :message="form.errors.cost" />
                </div>
                <div class="flex flex-col gap-2 col-span-2">
                    <label for="description">Description (Optional)</label>
                    <Textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        :invalid="form.errors.description"
                    />
                    <InputError :message="form.errors.description" />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <Button
                    type="button"
                    label="Cancel"
                    severity="secondary"
                    @click="closeModal"
                ></Button>
                <Button
                    type="submit"
                    label="Save"
                    :loading="form.processing"
                ></Button>
            </div>
        </form>
    </Dialog>
</template>
