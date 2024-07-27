<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    ticket: Array,
});
</script>

<template>

    <Head title="Ver Chamados" />

    <AuthenticatedLayout>
        <div class="max-w-screen-xl m-auto p-5 flex flex-col gap-5">
            
            <div class="rounded-md shadow-sm border px-5 py-10 text-center bg-white">Chamado de protocolo número {{ ticket.id }} </div>

        </div>
    </AuthenticatedLayout>
</template>
