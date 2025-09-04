<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import Menu from "primevue/menu";
import Avatar from "primevue/avatar";
import Button from "primevue/button";
import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";

// State untuk kontrol sidebar mobile
const isSidebarVisible = ref(false);

const menu = ref();
const menuItems = ref([
    {
        label: "Navigation",
        items: [
            {
                label: "Dashboard",
                icon: "pi pi-home",
                route: "dashboard",
            },
            {
                label: "Categories",
                icon: "pi pi-tags",
                route: "categories.index",
            },
        ],
    },
]);

// Untuk dropdown user
const userMenu = ref();
const userMenuItems = ref([
    { label: "Profile", icon: "pi pi-user", route: "profile.edit" },
    {
        label: "Log Out",
        icon: "pi pi-sign-out",
        route: "logout",
        method: "post",
    },
]);

const toggleUserMenu = (event) => {
    userMenu.value.toggle(event);
};
</script>

<template>
    <Toast />
    <ConfirmDialog />
    <div class="min-h-screen bg-gray-100 relative">
        <aside
            class="w-64 bg-white border-r fixed inset-y-0 left-0 transform transition-transform duration-300 ease-in-out z-30 lg:translate-x-0 flex flex-col"
            :class="isSidebarVisible ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex-grow">
                <div class="p-4 border-b">
                    <Link :href="route('dashboard')">
                        <h1 class="text-2xl font-bold text-gray-800">
                            Inventro
                        </h1>
                    </Link>
                </div>
                <Menu :model="menuItems" class="w-full border-none">
                    <template #item="{ item }">
                        <Link
                            :href="item.route ? route(item.route) : '#'"
                            class="flex items-center p-3 text-gray-700 hover:bg-gray-200 rounded-md transition-colors duration-200"
                            :class="{
                                'bg-blue-100 text-blue-700 font-bold':
                                    item.route && route().current(item.route),
                            }"
                        >
                            <span :class="item.icon" class="mr-3"></span>
                            <span>{{ item.label }}</span>
                        </Link>
                    </template>
                </Menu>
            </div>

            <div class="p-4 border-t">
                <button
                    @click="toggleUserMenu"
                    class="w-full flex items-center space-x-2 p-2 rounded-md hover:bg-gray-100"
                >
                    <Avatar
                        :label="
                            $page.props.auth.user.name.charAt(0).toUpperCase()
                        "
                        shape="circle"
                    />
                    <span class="font-semibold text-gray-700">{{
                        $page.props.auth.user.name
                    }}</span>
                </button>
                <Menu
                    ref="userMenu"
                    :model="userMenuItems"
                    :popup="true"
                    class="w-56"
                >
                    <template #item="{ item }">
                        <Link
                            :href="route(item.route)"
                            :method="item.method || 'get'"
                            as="button"
                            class="w-full text-left flex items-center px-4 py-2 text-gray-700 hover:bg-gray-200"
                        >
                            <span :class="item.icon" class="mr-2"></span>
                            <span>{{ item.label }}</span>
                        </Link>
                    </template>
                </Menu>
            </div>
        </aside>

        <div class="lg:ml-64 transition-all duration-300 ease-in-out">
            <header
                class="flex justify-between items-center p-4 bg-white border-b sticky top-0 z-20 lg:hidden"
            >
                <Button
                    icon="pi pi-bars"
                    @click="isSidebarVisible = !isSidebarVisible"
                    text
                    rounded
                />
            </header>

            <main class="p-6">
                <div v-if="$slots.header" class="mb-6">
                    <slot name="header" />
                </div>
                <slot />
            </main>
        </div>

        <div
            v-if="isSidebarVisible"
            @click="isSidebarVisible = false"
            class="fixed inset-0 bg-black opacity-50 z-20 lg:hidden"
        ></div>
    </div>
</template>

<style></style>
