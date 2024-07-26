<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    company: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
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
    <GuestLayout>

        <Head title="Registrar" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nome" />

                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                    autocomplete="off" />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                    autocomplete="off" />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="company" value="Empresa" />

                <TextInput id="company" type="text" class="mt-1 block w-full" v-model="form.company" required
                    autocomplete="off" />

                <InputError class="mt-2" :message="form.errors.company" />
            </div>

            <div class="mt-4">
                <InputLabel for="phone" value="Telefone" />

                <TextInput id="phone" type="text" class="mt-1 block w-full" v-model="form.phone" @input="formatPhone"
                    required autocomplete="off" />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Senha" />

                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                    autocomplete="off" />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmar Senha" />

                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                    v-model="form.password_confirmation" required autocomplete="off" />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#5d5fb0]">
                Já tem uma conta?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Registrar
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
