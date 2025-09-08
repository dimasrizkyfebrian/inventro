<script setup>
import { useForm } from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import Dropdown from "primevue/dropdown";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    visible: Boolean,
    products: Array,
});
const emit = defineEmits(["close"]);

const form = useForm({
    product_id: null,
    type: null,
    quantity: 1,
    reason: "",
});

const adjustmentTypes = [
    { label: "Addition (+)", value: "addition" },
    { label: "Subtraction (-)", value: "subtraction" },
];

const submit = () => {
    form.post(route("stock-adjustments.store"), {
        onSuccess: () => closeModal(),
    });
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
        header="New Stock Adjustment"
        :style="{ width: '30rem' }"
        @update:visible="closeModal"
    >
        <form @submit.prevent="submit" class="flex flex-col gap-4">
            <div>
                <label for="product">Product</label>
                <Dropdown
                    v-model="form.product_id"
                    :options="products"
                    filter
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Select a Product"
                    class="w-full mt-1"
                    :invalid="form.errors.product_id"
                />
                <InputError :message="form.errors.product_id" />
            </div>
            <div>
                <label for="type">Adjustment Type</label>
                <Dropdown
                    v-model="form.type"
                    :options="adjustmentTypes"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Select a Type"
                    class="w-full mt-1"
                    :invalid="form.errors.type"
                />
                <InputError :message="form.errors.type" />
            </div>
            <div>
                <label for="quantity">Quantity</label>
                <InputNumber
                    v-model="form.quantity"
                    class="w-full mt-1"
                    :min="1"
                    :invalid="form.errors.quantity"
                />
                <InputError :message="form.errors.quantity" />
            </div>
            <div>
                <label for="reason">Reason</label>
                <InputText
                    v-model="form.reason"
                    class="w-full mt-1"
                    :invalid="form.errors.reason"
                />
                <InputError :message="form.errors.reason" />
            </div>

            <div class="flex justify-end gap-2 mt-4">
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
