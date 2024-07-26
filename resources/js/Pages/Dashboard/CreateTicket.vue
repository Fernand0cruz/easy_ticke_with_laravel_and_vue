<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Quill from 'quill'; // Importando o Quill
import 'quill/dist/quill.snow.css'; // Importa os estilos do Quill

const props = defineProps({
    departments: Array
});

const form = useForm({
    department_id: '',
    title: '',
    description: '', // Isso armazenará o conteúdo HTML do Quill
    priority: '',
});

// Referência para o editor Quill
const editor = ref(null);
let quill;

// Inicializando o Quill
onMounted(() => {
    quill = new Quill(editor.value, {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'size': [] }],
                ['bold', 'italic', 'underline'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['link', 'image'],
            ],
        },
    });

    // Atualiza o campo de descrição no formulário quando o conteúdo muda
    quill.on('text-change', () => {
        form.description = quill.root.innerHTML; // Armazena o conteúdo HTML no form
    });
});

const submit = () => {
    form.post(route('createTicket.store'), {
        onFinish: () => {
            if (Object.keys(form.errors).length === 0) {
                window.location.reload();
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
                        <select id="department_id"
                            class="mt-1 block w-full border-gray-300 focus:border-[#5d5fb0] focus:ring-[#8789FE] rounded-md shadow-sm"
                            v-model="form.department_id" autofocus required>
                            <option v-for="department in departments" :key="department.id" :value="department.id">
                                {{ department.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.department" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="title" value="Assunto do chamado" />
                        <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required
                            autocomplete="off" />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="description" value="Descrição do chamado" class="mb-1"/>
                        <div ref="editor" class="quill-editor mt-1 block w-full border-gray-300 shadow-sm" required></div>
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="priority" value="Prioridade"/>
                        <select id="priority"
                            class="mt-1 block w-full border-gray-300 focus:border-[#5d5fb0] focus:ring-[#8789FE] rounded-md shadow-sm"
                            v-model="form.priority" required>
                            <option value="high">Alta</option>
                            <option value="medium">Média</option>
                            <option value="low" selected>Baixa</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.priority" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <PrimaryButton class="ms-4 flex gap-2" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
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

