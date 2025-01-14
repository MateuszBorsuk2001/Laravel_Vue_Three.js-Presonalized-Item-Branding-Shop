<template>
    <div class="product-container relative overflow-hidden" @mouseenter="showSlider = true" @mouseleave="showSlider = false">
        <div class="product-card">
            <img :src="image" alt="Product Image" class="product-image" />
            <div class="product-description">
                <h2>{{ name }}</h2>
                <p>{{ description }}</p>
            </div>
        </div>
        
        <!-- Sliding Action Buttons -->
        <div class="action-slider" :class="{ 'slide-up': showSlider }">
            <button @click.stop="removeDesign" class="action-button delete-button">
                <span class="text-white text-xl">×</span>
            </button>
            <button @click.stop="addToBasket" class="action-button add-button">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { emitter } from '@/eventBus';
import axios from 'axios';

const showSlider = ref(false);

const removeDesign = async () => {
    await axios.delete(`/api/items/${props.id}`);
    emitter.emit('designRemoved');
};

const addToBasket = async () => {
    await axios.post('/api/basket', {
        name: props.name,
        quantity: 1
    });
    emitter.emit('basketUpdated');
};
</script>

<style scoped>
.action-slider {
    position: absolute;
    bottom: -60px;
    left: 0;
    right: 0;
    height: 60px;
    display: flex;
    transition: transform 0.3s ease;
}

.slide-up {
    transform: translateY(-60px);
}

.action-button {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
}

.delete-button {
    background-color: #EF4444;
}

.add-button {
    background-color: #10B981;
}
</style>
