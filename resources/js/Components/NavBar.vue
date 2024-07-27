<script setup>
import { ref } from 'vue';
import { AlignJustify, CircleUserRound, X, House, Bolt, UserRoundCheck, ListCheck, Ticket, TicketCheck, TicketPlus, Package2, MailQuestion } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

const open = ref(false);
const toggleMenu = () => open.value = !open.value;

const beforeEnter = (el) => {
    el.style.transform = 'translateX(-100%)';
};

const enter = (el, done) => {
    el.offsetHeight; // Força reflow
    el.style.transition = 'transform .5s ease-out, opacity .5s ease-out';
    el.style.transform = 'translateX(0)';
    done();
};

const leave = (el, done) => {
    el.style.transition = 'transform .5s ease-out, opacity .5s ease-out';
    el.style.transform = 'translateX(-100%)';
    // Espera a animação terminar antes de remover o elemento
    setTimeout(done, 500); // Dura o mesmo que a transição
};
</script>

<template>
    <nav class="border shadow-sm rounded-md bg-white">
        <div class="flex justify-between items-center max-w-screen-xl m-auto px-5 py-3">
            <div class="flex items-center gap-5">
                <button @click="toggleMenu" class="flex gap-2 border p-2 shadow-sm rounded-md">
                    <AlignJustify />
                </button>
                <Link href="/dashboard" class="uppercase">
                {{ $page.props.auth.user.company.name }}
                </Link>
            </div>
            <div>
                <button class="flex gap-2 border p-2 shadow-sm rounded-md">
                    Olá, {{ $page.props.auth.user.name }}
                    <CircleUserRound />
                </button>
            </div>
        </div>

        <div v-if="open" class="fixed inset-0 bg-black bg-opacity-80 z-40" @click="open = false"></div>

        <transition @before-enter="beforeEnter" @enter="enter" @leave="leave">
            <div v-if="open" class="fixed inset-y-0 left-0 w-96 bg-white shadow-md z-50 p-5">
                <div class="flex justify-end items-center">
                    <button @click="toggleMenu" class="border rounded-md shadow-sm">
                        <X />
                    </button>
                </div>

                <ul>
                    <li>
                        <Link href="/dashboard" class="py-2 flex gap-2 items-center">
                        <House />Página inicial
                        </Link>
                    </li>
                    <li>
                        <Link href="#" class="py-2 flex gap-2 items-center">
                        <Bolt />Admin configs
                        </Link>
                    </li>
                    <li>
                        <Link :href="route('users.index')" class="py-2 flex gap-2 items-center">
                        <UserRoundCheck />Usuários
                        </Link>
                    </li>
                    <li>
                        <Link :href="route('departments.index')" class="py-2 flex gap-2 items-center">
                        <ListCheck />Departamentos
                        </Link>
                    </li>
                    <li>
                        <Link :href="route('allTickets.show')" class="py-2 flex gap-2 items-center">
                        <Ticket />Todos os chamados
                        </Link>
                    </li>
                    <li>
                        <Link :href="route('createTicket.index')" class="py-2 flex gap-2 items-center">
                        <Ticket />Criar Chamado
                        </Link>
                    </li>
                    <li>
                        <Link :href="route('tickets.show')" class="py-2 flex gap-2 items-center">
                        <Ticket />Chamados
                        </Link>
                    </li>
                    <li>
                        <Link :href="route('myTickets.show')" class="py-2 flex gap-2 items-center">
                        <TicketCheck />Meus Chamados
                        </Link>
                    </li>
                    <li>
                        <Link href="#" class="py-2 flex gap-2 items-center">
                        <TicketPlus />Abrir chamado para admin
                        </Link>
                    </li>
                    <li>
                        <Link href="#" class="py-2 flex gap-2 items-center">
                        <Package2 />Base de arquivos
                        </Link>
                    </li>
                    <li>
                        <Link href="#" class="py-2 flex gap-2 items-center">
                        <MailQuestion />Suporte Easy Ticket
                        </Link>
                    </li>




                    <Link :href="route('logout')" method="post">
                    Sair
                    </Link>



                </ul>
            </div>
        </transition>
    </nav>
</template>
