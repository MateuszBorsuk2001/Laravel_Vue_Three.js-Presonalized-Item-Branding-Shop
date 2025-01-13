<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Carousel from '@/Components/Carousel.vue';

</script>

<template>
    <Head title="Create" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stwórz swój własny produkt</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 lg:h-[720px] lg:w-[1280px]">
                <Carousel>
                </Carousel>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.bg-c{
    background-color: rgb(238, 238, 238);
}

</style>