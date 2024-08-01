<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, watch } from 'vue';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Inertia } from '@inertiajs/inertia';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, useForm } from '@inertiajs/vue3';

// Define as props recebidas do servidor
const props = defineProps({
    user: Object, // Objeto que contém informações do usuário
    ticket: Object, // Objeto que contém informações do chamado
    responses: Array, // Array de respostas ao chamado
});

// Função para formatar a data em 'dd/MM/yyyy HH:mm'
const formatDate = (date) => { return format(new Date(date), 'dd/MM/yyyy HH:mm', { locale: ptBR }); }

// Função para atribuir o chamado a um usuário
const assignToUser = (ticketId) => {
    Inertia.patch(`/dashboard/ticket/${ticketId}/assign`, {}, {
        onFinish: () => {
            window.location.reload(); // Recarrega a página após a atribuição
        },
    });
}

// Inicializa o formulário de resposta
const form = useForm({
    response: '', // Campo de resposta do formulário
});

// Referência para o editor Quill
const editor = ref(null);
let quill;

// Função para inicializar o editor Quill
const initializeQuill = () => {
    if (editor.value) {
        quill = new Quill(editor.value, {
            theme: 'snow', // Define o tema do editor
            modules: {
                toolbar: [ // Configura a barra de ferramentas do editor
                    [{ 'size': [] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['link', 'image'],
                ],
            },
        });
        // Atualiza o campo de resposta do formulário quando o texto muda
        quill.on('text-change', () => {
            form.response = quill.root.innerHTML;
        });
    } else {
        console.error('Editor element not found or ticket is not assigned');
    }
};

// Executa quando o componente é montado
onMounted(() => {
    // Verifica se o chamado está atribuído a um usuário
    if (props.ticket.assigned_to_user_id !== null) {
        initializeQuill(); // Inicializa o editor
    }
});

// Observa mudanças na atribuição do chamado
watch(() => props.ticket.assigned_to_user_id, (newValue) => {
    if (newValue !== null) {
        initializeQuill(); // Inicializa o editor se o chamado for atribuído
    }
});

// Função para submeter a resposta
const submit = () => {
    if (quill.root.innerHTML.trim() === '<p><br></p>') {
        form.errors.response = 'A resposta não pode estar vazia'; // Validação de resposta vazia
        return;
    }
    form.post(route('ticket.response', { id: props.ticket.id }), {
        onFinish: () => {
            if (Object.keys(form.errors).length === 0) {
                window.location.reload(); // Recarrega a página se não houver erros
            }
        },
    });
};
</script>

<template>
    <Head title="Ver Chamado" />
    <AuthenticatedLayout>
        <div class="max-w-screen-xl m-auto p-5 flex flex-col gap-5">
            <div class="rounded-md shadow-sm border px-5 py-10 text-center bg-white">
                Detalhes do Chamado de protocolo N. {{ ticket.id }}
            </div>
            <div class="flex w-full gap-5">
                <div class="rounded-md shadow-sm border px-5 py-10 text-center bg-white w-3/5 flex flex-col gap-5">
                    <p class="text-justify">Assunto: {{ ticket.title }}</p>
                    <div v-if="ticket.assigned_to_user_id !== null && user.id === ticket.assigned_to_user_id"
                        class="text-justify">
                        Este chamado agora está sob sua responsabilidade
                    </div>
                    <div>
                        <div class="flex justify-between">
                            <span>{{ ticket.user.name }} - {{ ticket.opened_by_department.name }}</span>
                            <span>{{ formatDate(ticket.created_at) }}</span>
                        </div>
                        <div v-html="ticket.description" class="text-justify border p-5 rounded-md shadow-sm"></div>
                    </div>
                    <div v-if="responses.length > 0" class="flex flex-col gap-5">
                        <div v-for="response in responses" :key="response.id">
                            <div class="flex justify-between">
                                <span>{{ response.user.name }} - {{ response.user.department.name }}</span>
                                <span>{{ formatDate(response.created_at) }}</span>
                            </div>
                            <div v-html="response.response" class="text-justify border p-5 rounded-md shadow-sm"></div>
                        </div>
                    </div>
                    <div v-if="ticket.assigned_to_user_id !== null && (user.id === ticket.user_id || user.id === ticket.assigned_to_user_id)">
                        <form @submit.prevent="submit">
                            <div class="mt-4 text-justify">
                                <InputLabel for="response" value="Enviar resposta para o chamado:" class="mb-1" />
                                <div ref="editor" class="quill-editor mt-1 block w-full border-gray-300 shadow-sm" required></div>
                                <InputError class="mt-2" :message="form.errors.response" />
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton class="ms-4 flex gap-2" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Enviar resposta
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="rounded-md shadow-sm border px-5 py-10 bg-white flex flex-col gap-2 w-2/5">
                    <p>Prioridade: {{ ticket.priority }}</p>
                    <p>Criado por: {{ ticket.user.name }}</p>
                    <p>Email: {{ ticket.user.email }}</p>
                    <p>Departamento: {{ ticket.opened_by_department.name }}</p>
                    <p>Data/Hora de criação: {{ formatDate(ticket.created_at) }}</p>
                    <div class="flex flex-col gap-2 mt-5">
                        <PrimaryButton @click="assignToUser(ticket.id)" v-if="user.role === 'admin' || ticket.opened_by_department_id !== user.department_id && ticket.assigned_to_user_id === null">
                            Me Vincular ao Chamado
                        </PrimaryButton>
                        <PrimaryButton v-if="user.role === 'admin' || ticket.assigned_to_user_id === user.id">
                            Transferir Chamado
                        </PrimaryButton>
                        <PrimaryButton v-if="user.role === 'admin' || ticket.user_id === user.id && ticket.assigned_to_user_id === null">
                            Editar Chamado
                        </PrimaryButton>
                        <DangerButton v-if="user.role === 'admin' || user.id === ticket.user_id || user.id === ticket.assigned_to_user_id">
                            Encerrar Chamado
                        </DangerButton>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.quill-editor {
    height: 200px;
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    font-weight: 400;
    font-style: normal;
}
</style>
