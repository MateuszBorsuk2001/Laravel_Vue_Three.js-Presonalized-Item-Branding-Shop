<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold mb-8">Finalizacja zamówienia</h2>
            
            <!-- Cart Items -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Twoje przedmioty</h2>
                <div class="space-y-4">
                    <div v-for="item in basketItems" :key="item.id" class="flex items-center border p-4 rounded">
                        <img :src="item.item.screenshot_path" class="w-20 h-20 object-cover mr-4">
                        <div class="flex-grow">
                            <h3 class="font-medium">{{ item.model }}</h3>
                            <p>Ilość sztuk: {{ item.quantity }}</p>
                            <p>Cena za sztukę: {{ item.item.unit_price }} €$</p>
                            <p>Razem: {{ calcuateTotalPerItem(item) }} €$</p>
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
                </div>
                <div class="mt-4 text-xl font-bold">
                    Total Price: {{ calculateTotal() }} €$
                </div>
            </div>

            <!-- Order Form -->
            <form @submit.prevent="submitOrder" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1">Imię</label>
                        <input 
                            type="text" 
                            v-model="form.name" 
                            :class="[
                                'w-full border rounded p-2',
                                form.errors.name ? 'border-red-500' : 'border-gray-300'
                            ]"
                            required
                        >
                    </div>
                    <div>
                        <label class="block mb-1">Nazwisko</label>
                        <input 
                            type="text" 
                            v-model="form.surname" 
                            :class="[
                                'w-full border rounded p-2',
                                form.errors.surname ? 'border-red-500' : 'border-gray-300'
                            ]"
                            required
                        >
                    </div>
                    <div>
                        <label class="block mb-1">Email</label>
                        <input 
                            type="email" 
                            v-model="form.email" 
                            :class="[
                                'w-full border rounded p-2',
                                form.errors.email ? 'border-red-500' : 'border-gray-300'
                            ]"
                            required
                        >
                    </div>
                    <div>
                        <label class="block mb-1">Kod pocztowy</label>
                        <input 
                            type="text" 
                            v-model="form.postal_code" 
                            :class="[
                                'w-full border rounded p-2',
                                form.errors.postal_code ? 'border-red-500' : 'border-gray-300'
                            ]"
                            required
                        >
                    </div>
                    <div>
                        <label class="block mb-1">Ulica</label>
                        <input 
                            type="text" 
                            v-model="form.street_name" 
                            :class="[
                                'w-full border rounded p-2',
                                form.errors.street_name ? 'border-red-500' : 'border-gray-300'
                            ]"
                            required
                        >
                    </div>
                    <div>
                        <label class="block mb-1">Numer domu</label>
                        <input 
                            type="text" 
                            v-model="form.house_number" 
                            :class="[
                                'w-full border rounded p-2',
                                form.errors.house_number ? 'border-red-500' : 'border-gray-300'
                            ]"
                            required
                        >
                    </div>
                    <div>
                        <label class="block mb-1">Miasto</label>
                        <input 
                            type="text" 
                            v-model="form.city" 
                            :class="[
                                'w-full border rounded p-2',
                                form.errors.city ? 'border-red-500' : 'border-gray-300'
                            ]"
                            required
                        >
                    </div>
                </div>
                  <div class="flex gap-4">
                      <button 
                          type="submit" 
                          :disabled="orderComplete"
                          class="text-white px-6 py-2 rounded transition-colors duration-200"
                          :class="[
                              orderComplete ? 'bg-green-500 cursor-not-allowed' : 'bg-indigo-500 hover:bg-indigo-600'
                          ]"
                      >
                          {{ orderComplete ? 'Zapłacono' : 'Zapłać za zakupy' }}
                      </button>
                      <button 
                          type="button"
                          v-if="orderComplete"
                          @click="downloadConfirmation"
                          class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded transition-colors duration-200"
                      >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                          </svg>
                          Pobierz potwierdzenie
                      </button>
                  </div>
              </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted } from 'vue'
import { emitter } from '@/eventBus'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const orderComplete = ref(false)
const basketItems = ref([])
const orderId = ref(null); 

const form = ref({
    name: '',
    surname: '',
    email: '',
    postal_code: '',
    street_name: '',
    house_number: '',
    city: '',
    errors: {}
})
  onMounted(() => {
      fetchBasketItems()
      emitter.on('basketUpdated', fetchBasketItems)
  })

  const fetchBasketItems = async () => {
      try {
          const response = await axios.get('/api/basket')
          basketItems.value = response.data
      } catch (error) {
          console.error('Error fetching basket items:', error)
      }
  }

  const calculateTotal = () => {
      return basketItems.value.reduce((total, item) => {
          return total + (item.item.unit_price * item.quantity)
      }, 0)
  }

  const calcuateTotalPerItem = (item) => {
      return item.item.unit_price * item.quantity
  }
  const submitOrder = async () => {
      const firstItem = basketItems.value[0];
      const orderData = {
          ...form.value,
          items: basketItems.value,
          total_price: calculateTotal(),
      }

      try {
          await axios.post('/orders', orderData)
          orderComplete.value = true
        
          const orderResponse = await axios.get('/api/latest-order')
          orderId.value = orderResponse.data.id
        
      } catch (error) {
          console.error('Order submission failed:', error)
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

const downloadConfirmation = async () => {
    try {
        if (!orderId.value) {
            console.error('Order ID is not set');
            return;
        }

        const response = await axios.get(`/order/confirmation/${orderId.value}/download`, {
            responseType: 'blob'
        });

        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'potwierdzenie-zamowienia.pdf';
        link.click();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error downloading confirmation:', error);
    }
}</script>

