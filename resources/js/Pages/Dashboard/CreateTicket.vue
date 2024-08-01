<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { ClipboardPlus } from 'lucide-vue-next';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

// Define as propriedades esperadas na view
const props = defineProps({
    departments: Array // Espera um array de departamentos
});

// Inicializa o formulário usando useForm do Inertia
const form = useForm({
    department_id: '', // ID do departamento selecionado
    title: '', // Título do chamado
    description: '', // Descrição do chamado (armazena conteúdo HTML do Quill)
    priority: '', // Prioridade do chamado
});

// Referência para o editor Quill
const editor = ref(null); // Cria uma referência reativa para o editor
let quill; // Variável para armazenar a instância do Quill

// Inicializando o Quill quando o componente for montado
onMounted(() => {
    quill = new Quill(editor.value, {
        theme: 'snow', // Define o tema do Quill
        modules: {
            toolbar: [ // Configuração da barra de ferramentas do Quill
                [{ 'size': [] }], // Tamanho da fonte
                ['bold', 'italic', 'underline'], // Estilos de texto
                [{ 'color': [] }, { 'background': [] }], // Cores de texto e fundo
                [{ 'list': 'ordered' }, { 'list': 'bullet' }], // Listas ordenadas e não ordenadas
                ['link', 'image'], // Opções para adicionar links e imagens
            ],
        },
    });

    // Atualiza o campo de descrição no formulário quando o conteúdo muda
    quill.on('text-change', () => {
        form.description = quill.root.innerHTML; // Armazena o conteúdo HTML no form
    });
});

// Função para enviar o formulário
const submit = () => {
    form.post(route('createTicket.store'), { // Envia os dados do formulário para a rota definida
        onFinish: () => {
            if (Object.keys(form.errors).length === 0) { // Se não houver erros
                window.location.reload(); // Recarrega a página
            }
        },
    });
};
</script>

<template>
    <Head title="Criar Ticket" />
    <AuthenticatedLayout>
        <div class="max-w-screen-xl m-auto p-5 flex flex-col gap-5">
            <div class="rounded-md shadow-sm border px-5 py-10 text-center bg-white">Criar Chamado</div>
            <div class="rounded-md shadow-sm border p-5 flex flex-col gap-5 bg-white">
                <h1>Criar Chamado</h1>
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div>
                        <InputLabel for="department_id" value="Departamento" />
                        <select id="department_id" class="mt-1 block w-full border-gray-300 focus:border-[#5d5fb0] focus:ring-[#8789FE] rounded-md shadow-sm" v-model="form.department_id" autofocus required>
                            <option v-for="department in departments" :key="department.id" :value="department.id">
                                {{ department.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.department" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="title" value="Assunto do chamado" />
                        <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autocomplete="off" />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="description" value="Descrição do chamado" class="mb-1" />
                        <div ref="editor" class="quill-editor mt-1 block w-full border-gray-300 shadow-sm" required></div>
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="priority" value="Prioridade" />
                        <select id="priority" class="mt-1 block w-full border-gray-300 focus:border-[#5d5fb0] focus:ring-[#8789FE] rounded-md shadow-sm" v-model="form.priority" required>
                            <option value="high">Alta</option>
                            <option value="medium">Média</option>
                            <option value="low" selected>Baixa</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.priority" />
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <PrimaryButton class="ms-4 flex gap-2" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            <ClipboardPlus />
                            Criar Chamado
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.quill-editor {
    height: 300px;
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    font-weight: 400;
    font-style: normal;
}
</style>
