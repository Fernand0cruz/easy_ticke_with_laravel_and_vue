<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link } from '@inertiajs/vue3';

// DefineProps permite definir as propriedades que o componente espera receber
const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <!-- Componente Head para gerenciar o título da página -->

    <Head title="Home" />

    <!-- Barra de navegação -->
    <nav v-if="canLogin" class="border shadow-sm p-5">
        <div class="max-w-screen-xl m-auto flex justify-between items-center">
            <div class="w-[150px]">
                <Link href="/">
                    <ApplicationLogo />
                </Link>
            </div>
            <div class="flex gap-5">
                <!-- Links condicionais dependendo do estado de autenticação -->
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="hover:text-[#8789FE]">Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')" class="hover:text-[#8789FE]">Entrar</Link>
                    <Link v-if="canRegister" :href="route('register')" class="hover:text-[#8789FE]">Registrar</Link>
                </template>
            </div>
        </div>
    </nav>

    <!-- Conteúdo principal -->
    <main class="flex-1">
        <div class="max-w-screen-xl m-auto">
            <div class="flex flex-col items-center justify-center h-[calc(100vh-135px)]">
                <h1 class="text-4xl font-bold text-center">Bem-vindo ao Easy Ticket</h1>
                <p class="text-center mt-4">O sistema de gerenciamento de tickets mais fácil e completo!</p>
            </div>
        </div>
    </main>
    <!-- Rodapé -->
    <footer class="border shadow-sm p-5">
        <div class="max-w-screen-xl m-auto">
            <h1>Copyright (C) 2024
                <Link href="https://github.com/Fernand0cruz" className="font-bold">Fernand0W</Link>
            </h1>
        </div>
    </footer>
</template>
