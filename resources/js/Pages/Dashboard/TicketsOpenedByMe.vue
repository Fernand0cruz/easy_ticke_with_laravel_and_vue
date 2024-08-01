<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale'; // Importando a localização em português
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { SquareArrowOutUpRight } from 'lucide-vue-next';

// Define as props recebidas do servidor
const props = defineProps({
    tickets: Array, // Array de chamados a serem exibidos
});

// Função para formatar a data no formato 'dd/MM/yyyy'
const formatDate = (date) => {
    return format(new Date(date), 'dd/MM/yyyy', { locale: ptBR });
}
</script>

<template>
    <Head title="Ver Chamados" />
    <AuthenticatedLayout>
        <div class="max-w-screen-xl m-auto p-5 flex flex-col gap-5">
            <div class="rounded-md shadow-sm border px-5 py-10 text-center bg-white">Meus Chamados</div>
            <div class="rounded-md shadow-sm border p-5 flex flex-col gap-5 bg-white">
                <h1>Lista de Chamados</h1>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ver Chamado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Protocolo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assunto</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">De</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado Por</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Para</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridade</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="ticket in tickets" :key="ticket.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Link :href="`/dashboard/ticket/${ticket.id}`">
                                        <PrimaryButton>
                                            <SquareArrowOutUpRight />
                                        </PrimaryButton>
                                    </Link>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ticket.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ticket.title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ticket.opened_by_department.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ticket.user.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ticket.department.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ticket.priority }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(ticket.created_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
