<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="max-w-screen-xl m-auto p-5">
            <div class="rounded-md shadow-sm border px-5 py-10 text-center bg-white">Bem Vindo, {{ $page.props.auth.user.name }}!</div>
        </div>
    </AuthenticatedLayout>
</template>
