<script setup>
import { useForm } from "@inertiajs/vue3";
import { watch, computed } from "vue";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    visible: Boolean,
    category: {
        type: Object,
        default: null,
    },
});
const emit = defineEmits(["close"]);

const isEditMode = computed(() => !!props.category);

const form = useForm({
    name: "",
});

watch(
    () => props.category,
    (newCategory) => {
        if (newCategory) {
            form.name = newCategory.name;
        } else {
            form.reset();
        }
    }
);

const submit = () => {
    if (isEditMode.value) {
        form.put(route("categories.update", props.category.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("categories.store"), {
            onSuccess: () => closeModal(),
        });
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
        :header="isEditMode ? 'Edit Category' : 'Add New Category'"
        :style="{ width: '25rem' }"
        @update:visible="closeModal"
    >
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-2 mb-4">
                <label for="name">Category Name</label>
                <InputText
                    id="name"
                    v-model="form.name"
                    required
                    autofocus
                    :invalid="form.errors.name"
                />
                <InputError :message="form.errors.name" />
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
