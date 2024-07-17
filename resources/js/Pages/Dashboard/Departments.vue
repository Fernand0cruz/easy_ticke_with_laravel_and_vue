<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import DangerButton from '@/Components/DangerButton.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Trash2, Pencil } from 'lucide-vue-next';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

// Recebe as props do servidor
const props = defineProps({
    user: Object,
    departments: Array
});

const form = useForm({
    name: '',
    email: '',
    location: '',
    phone: '',
});

const submit = () => {
    form.post(route('departments.store'), {
        onFinish: () => window.location.reload(),
    });
};

// Função para aplicar a máscara de telefone com traço após os primeiros 5 dígitos
const applyPhoneMask = (value) => {
    // Remove caracteres não numéricos
    let numericValue = value.replace(/\D/g, '');

    // Aplica os parênteses nos dois primeiros dígitos
    if (numericValue.length > 2) {
        numericValue = `(${numericValue.slice(0, 2)})${numericValue.slice(2)}`;
    }

    // Aplica o traço após os primeiros 5 dígitos
    if (numericValue.length > 7) {
        numericValue = `${numericValue.slice(0, 9)}-${numericValue.slice(9)}`;
    }

    // Limita o número máximo de dígitos para 14 (incluindo parênteses e traço)
    numericValue = numericValue.slice(0, 14);

    return numericValue;
};

// Método para formatar o telefone enquanto o usuário digita
const formatPhone = () => {
    form.phone = applyPhoneMask(form.phone);
};
</script>

<template>

    <Head title="Departamento" />

    <AuthenticatedLayout>
        <div class="max-w-screen-xl m-auto p-5 flex flex-col gap-5">

            <div class="rounded-md shadow-sm border px-5 py-10 text-center">Departamentos</div>

            <div class=" rounded-md shadow-sm border p-5 flex flex-col gap-5">

                <h1>Criar Departamento</h1>

                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="name" value="Nome do departamento" />

                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required
                            autofocus autocomplete="off" />

                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="email" value="Email" />

                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                            autocomplete="off" />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="location" value="Localização" />

                        <TextInput id="location" type="text" class="mt-1 block w-full" v-model="form.location"
                            autocomplete="off" />

                        <InputError class="mt-2" :message="form.errors.location" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="phone" value="Telefone" />

                        <TextInput id="phone" type="text" class="mt-1 block w-full" v-model="form.phone"
                            @input="formatPhone" autocomplete="off" />

                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                            Cadastrar departamento
                        </PrimaryButton>
                    </div>
                </form>
            </div>

            <!-- Listagem dos departamentos em uma tabela  -->
            <div class="rounded-md shadow-sm border p-5 flex flex-col gap-5">
                <h1>Lista de Departamentos</h1>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nome
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Telefone
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Excluir
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Editar
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="department in departments" :key="department.id">
                                <td class="px-6 py-4 whitespace-nowrap">{{ department.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ department.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ department.phone }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ department.status }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <DangerButton class="flex gap-2">
                                        <Trash2 />Excluir
                                    </DangerButton>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <PrimaryButton class="flex gap-2">
                                        <Pencil />Editar
                                    </PrimaryButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
