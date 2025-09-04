<script setup>
import { useForm } from "@inertiajs/vue3";
import { watch, computed } from "vue";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import Button from "primevue/button";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    visible: Boolean,
    supplier: { type: Object, default: null },
});
const emit = defineEmits(["close"]);

const isEditMode = computed(() => !!props.supplier);

const form = useForm({
    name: "",
    email: "",
    phone: "",
    address: "",
});

watch(
    () => props.supplier,
    (newVal) => {
        if (newVal) {
            form.name = newVal.name;
            form.email = newVal.email;
            form.phone = newVal.phone;
            form.address = newVal.address;
        } else {
            form.reset();
        }
    }
);

const submit = () => {
    if (isEditMode.value) {
        form.put(route("suppliers.update", props.supplier.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("suppliers.store"), { onSuccess: () => closeModal() });
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
        :header="isEditMode ? 'Edit Supplier' : 'Add New Supplier'"
        :style="{ width: '30rem' }"
        @update:visible="closeModal"
    >
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-3 mb-4">
                <div>
                    <label for="name">Name</label>
                    <InputText
                        id="name"
                        v-model="form.name"
                        class="w-full mt-1"
                        required
                        autofocus
                        :invalid="form.errors.name"
                    />
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <label for="email">Email</label>
                    <InputText
                        id="email"
                        v-model="form.email"
                        class="w-full mt-1"
                        required
                        :invalid="form.errors.email"
                    />
                    <InputError :message="form.errors.email" />
                </div>
                <div>
                    <label for="phone">Phone</label>
                    <InputText
                        id="phone"
                        v-model="form.phone"
                        class="w-full mt-1"
                        :invalid="form.errors.phone"
                    />
                    <InputError :message="form.errors.phone" />
                </div>
                <div>
                    <label for="address">Address</label>
                    <Textarea
                        id="address"
                        v-model="form.address"
                        class="w-full mt-1"
                        rows="3"
                        :invalid="form.errors.address"
                    />
                    <InputError :message="form.errors.address" />
                </div>
            </div>

            <div class="flex justify-end gap-2">
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
