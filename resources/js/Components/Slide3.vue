<template>
    <div class="summary-container h-full flex flex-col justify-between">
        <h3 class="text-2xl font-bold mb-4">Podsumowanie</h3>
        
        <div class="preview-section flex-grow">
            <img v-if="modelScreenshot" :src="modelScreenshot" alt="Model Screenshot" class="model-preview max-h-[400px] object-contain mx-auto" />
        </div>

        <div class="actions-section mb-8">
            <div class="actions-container">
                <button @click="addToBasket" class="basket-btn"  :class="{ 'animate-fill': isAnimating }">
                    Dodaj do koszyka
                </button>

                <button @click="togglePurchaseModal" class="buy-now-btn">
                    Kup teraz
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { emitter } from '@/eventBus';

const isAnimating = ref(false);

const props = defineProps({
    modelScreenshot: {
        type: String,
        default: '' 
    },
    itemId: {
        type: Number,
        required: true
    },
    modelName: {
        type: String,
        required: true
    },
});

const addToBasket = async () => {
    isAnimating.value = true;
    
    try {
        const response = await axios.post('/api/basket', {
            itemId: props.itemId,
            name: props.modelName,
            quantity: 1
        });
        emitter.emit('basketUpdated');
        setTimeout(() => {
            isAnimating.value = false;
        }, 200);
    }catch (error) {
        console.error('Error adding to basket:', error);
        isAnimating.value = false;
    }
};

</script>

<style scoped>
.animate-fill {
    animation: fillButton 0.5s ease forwards;
}

@keyframes fillButton {
    0% {
        background-color: #ffffff;
        color: rgb(99 102 241);
    }
    50% {
        background-color: rgb(99 102 241);
        color: white;
    }
    100% {
        background-color: #ffffff;
        color: rgb(99 102 241);
    }
}
.summary-container {
    padding: 20px;
    max-width: 800px;
    margin: 0 auto;
}

.actions-container {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 20px;
}

button {
    padding: 12px 24px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
}

.basket-btn {
    background-color: #ffffff;
    border: 2px solid rgb(99 102 241);
    color: rgb(99 102 241);
}

.buy-now-btn {
    background-color: rgb(99 102 241);
    border: none;
    color: white;
}

.purchase-form {
    max-height: 300px;
    overflow-y: auto;
    padding: 20px;
    border: 1px solid #e1e1e1;
    border-radius: 8px;
}

input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100%;
}

.proceed-btn {
    background-color: rgb(99 102 241);
    color: white;
    width: 100%;
}

.close-btn {
    font-size: 24px;
    background: none;
    border: none;
    padding: 0 8px;
}

.primary-btn {
    background-color: rgb(99 102 241);
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
}

.secondary-btn {
    background-color: #ffffff;
    border: 2px solid rgb(99 102 241);
    color: rgb(99 102 241);
    padding: 8px 16px;
    border-radius: 6px;
}
</style>
