<template>
    <div v-if="basketItems.length > 0">
        <div v-for="item in basketItems" :key="item.id" class="flex items-center justify-between py-2 border-b">
            <div class="flex items-center gap-2">
                <img :src="item.item.screenshot_path" class="w-12 h-12 object-cover rounded" />
                <span class="text-gray-600">x{{ item.quantity }}</span>
                <span class="text-gray-800">{{ translateModel(item.item.model) }}</span>
            </div>
            <button 
                @click="removeItem(item.id)"
                class="pr-1 hover:scale-110 transition-transform"
            >
                <svg class="w-6 h-6" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="11" fill="#EF4444"/>
                    <path d="M15.536 8.464a1 1 0 00-1.414 0L12 10.586l-2.122-2.122a1 1 0 00-1.414 1.414L10.586 12l-2.122 2.122a1 1 0 101.414 1.414L12 13.414l2.122 2.122a1 1 0 001.414-1.414L13.414 12l2.122-2.122a1 1 0 000-1.414z" 
                          fill="white"/>
                </svg>
            </button>
        </div>
        <div class="mt-4 text-right">
            <span class="font-bold">Total Items: {{ totalItems }}</span>
        </div>
    </div>
    <div v-else class="p-4 text-center text-gray-600">
       Twój koszyk jest pusty.
    </div>
</template>
<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { emitter } from '@/eventBus'

const isOpen = ref(false) 
let basketItems = ref([])

onMounted(() => {
    fetchBasketItems()
    emitter.on('basketUpdated', fetchBasketItems)
})

onUnmounted(() => {
    emitter.off('basketUpdated', fetchBasketItems)
})

const toggleBasket = () => {
    isOpen.value = !isOpen.value
    if (isOpen.value) {
        fetchBasketItems()
    }
}

const fetchBasketItems = async () => {
    try {
        const response = await axios.get('/api/basket')
        basketItems.value = response.data
        console.log('Basket updated:', basketItems.screenshot_path) // Add this to debug
    } catch (error) {
        console.error('Error fetching basket items:', error)
    }
}

const removeItem = async (itemId) => {
    try {
        await axios.delete(`/api/basket/${itemId}`)
        emitter.emit('basketUpdated')
    } catch (error) {
        console.error('Error removing item:', error)
    }
}

const translateModel = (model) => {
    const translations = {
        'tshirt': 'T-Shirt',
        'mug': 'Kubek',
        'cap': 'Czapka z daszkiem',
    }
    return translations[model] || model
}
</script>
