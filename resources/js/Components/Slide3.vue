<template>
    <div class="summary-container h-full flex flex-col justify-between">
        <h3 class="text-2xl font-bold mb-4">Podsumowanie</h3>
        
        <div class="preview-section flex-grow">
            <img v-if="modelScreenshot" :src="modelScreenshot" alt="Model Screenshot" class="model-preview max-h-[400px] object-contain mx-auto" />
        </div>

        <div class="actions-section mb-8">
            <div class="actions-container">
                <button @click="addToBasket" class="basket-btn">
                    Dodaj do koszyka
                </button>

                <button @click="togglePurchaseModal" class="buy-now-btn">
                    Kup teraz
                </button>
            </div>
        </div>

        <!-- Purchase Modal -->
        <div v-if="isModalVisible" class="modal-overlay" @click.self="togglePurchaseModal">
            <div class="modal-content">
                <form @submit.prevent="handlePurchase" class="grid gap-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-bold">Formularz zakupu</h4>
                        <button type="button" @click="togglePurchaseModal" class="close-btn">×</button>
                    </div>
                    <input type="text" v-model="purchaseForm.name" placeholder="Imię i nazwisko" required>
                    <input type="email" v-model="purchaseForm.email" placeholder="Email" required>
                    <input type="tel" v-model="purchaseForm.phone" placeholder="Telefon">
                    <input type="text" v-model="purchaseForm.address" placeholder="Adres dostawy" required>
                    <button type="submit" class="proceed-btn">Przejdź do płatności</button>
                </form>
            </div>
        </div>

        <!-- Basket Confirmation Modal -->
        <div v-if="isBasketModalVisible" class="modal-overlay" @click.self="toggleBasketModal">
            <div class="modal-content text-center">
                <div class="flex flex-col items-center gap-4">
                    <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h4 class="text-xl font-bold">Dodano do koszyka!</h4>
                    <div class="flex gap-4 mt-4">
                        <button @click="toggleBasketModal" class="secondary-btn">Kontynuuj zakupy</button>
                        <button @click="goToBasket" class="primary-btn">Przejdź do koszyka</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    modelScreenshot: {
        type: String,
        default: '' 
    }
});

const isModalVisible = ref(false);
const isBasketModalVisible = ref(false);
const purchaseForm = ref({
    name: '',
    email: '',
    phone: '',
    address: ''
});

const togglePurchaseModal = () => {
    isModalVisible.value = !isModalVisible.value;
};

const toggleBasketModal = () => {
    isBasketModalVisible.value = !isBasketModalVisible.value;
};

const addToBasket = () => {
    toggleBasketModal();
};

const goToBasket = () => {
    // Add basket navigation logic here
    toggleBasketModal();
};

const handlePurchase = () => {
    console.log('Form data:', purchaseForm.value);
    togglePurchaseModal();
};
</script>

<style scoped>
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

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
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
