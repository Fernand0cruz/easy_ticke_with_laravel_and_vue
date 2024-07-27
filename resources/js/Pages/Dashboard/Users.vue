<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ClipboardPlus, Trash2, Pencil } from 'lucide-vue-next';

const props = defineProps({
    user: Object,
    users: Array,
    departments: Array
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    department_id: '',
    status: 'active',
    role: 'user',
    password: '',
    password_confirmation: '',
});

const isEditing = ref(false);
const editUserId = ref(null);

const startEditUser = (user) => {
    form.name = user.name;
    form.email = user.email;
    form.phone = user.phone;
    form.department_id = user.department_id;
    form.status = user.status;
    form.role = user.role;
    editUserId.value = user.id;
    isEditing.value = true;
}

const submit = () => {
    if (isEditing.value) {
        form.patch(route('users.update', editUserId.value), {
            onFinish: () => {
                if (Object.keys(form.errors).length === 0) {
                    window.location.reload();
                }
            },
        });
    } else {
        form.post(route('users.store'), {
            onFinish: () => {
                if (Object.keys(form.errors).length === 0) {
                    window.location.reload();
                }
            },
        });
    }

};

const deleteUser = (id) => {
    form.delete(route('users.destroy', id), {
        onFinish: () => {
            props.users = props.users.filter(user => user.id !== id)
        }
    })
}


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

    <Head title="Usuários" />

    <AuthenticatedLayout>
        <div class="max-w-screen-xl m-auto p-5 flex flex-col gap-5">

            <div class="rounded-md shadow-sm border px-5 py-10 text-center bg-white">Usuários</div>

            <div class="rounded-md shadow-sm border p-5 flex flex-col gap-5 bg-white">

                <h1>{{ isEditing ? 'Editar Usuário' : 'Criar Usuário' }}</h1>

                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="name" value="Nome do usuário" />
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
                        <InputLabel for="phone" value="Telefone" />
                        <TextInput id="phone" type="text" class="mt-1 block w-full" v-model="form.phone"
                            @input="formatPhone" autocomplete="off" />
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="department_id" value="Departamento" />
                        <select id="department_id"
                            class="mt-1 block w-full border-gray-300 focus:border-[#5d5fb0] focus:ring-[#8789FE] rounded-md shadow-sm"
                            v-model="form.department_id" required>
                            <option v-for="department in departments" :key="department.id" :value="department.id">
                                {{ department.name }}
                            </option>

                        </select>
                        <InputError class="mt-2" :message="form.errors.department" />
                    </div>

                    <div class="mt-4" v-if="isEditing">
                        <InputLabel for="status" value="Status" />
                        <select id="status"
                            class="mt-1 block w-full border-gray-300 focus:border-[#5d5fb0] focus:ring-[#8789FE] rounded-md shadow-sm"
                            v-model="form.status" required>
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.status" />
                    </div>

                    <div class="mt-4" v-if="isEditing">
                        <InputLabel for="role" value="Role" />
                        <select id="role"
                            class="mt-1 block w-full border-gray-300 focus:border-[#5d5fb0] focus:ring-[#8789FE] rounded-md shadow-sm"
                            v-model="form.role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.role" />
                    </div>

                    <h1 class="text-red-600 mt-4">{{ isEditing ? 'Só é necessario inserir a senha se for mudar a mesma!'
                        : '' }}</h1>

                    <div class="mt-4">
                        <InputLabel for="password" value="Senha" />
                        <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password"
                            autocomplete="off" />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password_confirmation" value="Confirmar Senha" />
                        <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                            v-model="form.password_confirmation" autocomplete="off" />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <PrimaryButton class="ms-4 flex gap-2" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                            <ClipboardPlus />
                            {{ isEditing ? 'Atualizar Usuário' : 'Cadastrar Usuário' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>

            <!-- Listagem dos usuários em uma tabela  -->
            <div class="rounded-md shadow-sm border p-5 flex flex-col gap-5 bg-white">
                <h1>Lista de Usuários</h1>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nome</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Telefone</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Departamento</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Excluir</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Editar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users" :key="user.id">
                                <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ user.phone }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ user.department ? user.department.name :
                                    'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ user.status }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <DangerButton class="flex gap-2" @click="deleteUser(user.id)">
                                        <Trash2 />
                                    </DangerButton>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <PrimaryButton class="flex gap-2" @click="startEditUser(user)">
                                        <Pencil />
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
