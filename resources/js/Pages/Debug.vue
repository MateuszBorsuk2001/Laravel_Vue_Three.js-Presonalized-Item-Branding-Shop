<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    authenticated: Boolean,
    user: Object,
    session_id: String
});
</script>

<template>
    <Head title="Debug Auth" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <pre>{{ {authenticated, user, session_id} }}</pre>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
