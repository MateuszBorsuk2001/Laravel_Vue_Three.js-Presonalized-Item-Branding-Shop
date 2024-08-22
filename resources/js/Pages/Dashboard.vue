<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const dupas = [1,2,3,4,5,6,1,1,1,1,1]
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Twój sklep</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div>
                    <h3 class="font-semibold">Poprzednie pomysły</h3>
                <div class="w-full grid lg:grid-cols-5 md:grid-cols-3 grid-cols-1 gap-2 bg-green-400 p-5 justify-center">
                    <div v-for="dupa in dupas" class="w-full p-2 bg-red-400 border rounded text-center">Dupa</div>
                </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
