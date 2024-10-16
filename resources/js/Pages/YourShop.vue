<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
    products: {
        type: Array,
        required: true,
    }
});


</script>


<template>
    <Head title="YourShop" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Twój sklep</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div>
                    <h3 class="font-semibold">Poprzednie pomysły</h3>
                <div class="w-full grid lg:grid-cols-5 md:grid-cols-3 grid-cols-1 gap-2 p-5 justify-center">
                    <div v-for="product in products" :key="product.id" class="w-full text-center">
                        <ProductCard 
                            :image="product.image_url"
                            :name="product.name"
                            :description="product.description"
                        />
                    </div>
                </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

