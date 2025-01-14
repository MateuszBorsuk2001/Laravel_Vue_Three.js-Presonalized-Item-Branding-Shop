<template>
  <div class="product-container" @mouseenter="showSlider = true" @mouseleave="showSlider = false" @click="navigateToEditor">
      <div class="product-card">
          <img :src="image" alt="Product Image" class="product-image" />
          <div class="product-description">
              <h2>{{ name }}</h2>
              <p>{{ description }}</p>
          </div>
          <div class="action-slider" :class="{ 'slide-up': showSlider }">
            <button @click.stop="removeDesign" class="action-button delete-button">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
            <button @click.stop="addToBasket" class="action-button add-button">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </button>
          </div>
      </div>
  </div>
</template>
  
  <script setup>
  import { defineProps, ref } from 'vue';
  import { emitter } from '@/eventBus';
  import axios from 'axios';

  const props = defineProps({
    image: {
      type: String,
      required: true,
    },
    name: {
      type: String,
      required: true,
    },
    description: {
      type: String,
      required: true,
    },
    recordName: {
      type: String,
      required: true,
    },
  });

  const navigateToEditor = () => {
    const params = new URLSearchParams();
    params.append('screenshot_path', props.image);
    window.location.href = `/edit?${params.toString()}`;
};
  const showSlider = ref(false);
const removeDesign = async () => {
    try {
        await axios.delete(`/api/items/${props.recordName}`);
        // Pass the removed item name to the parent
        emitter.emit('designRemoved', props.recordName);
        emitter.emit('basketUpdated');
    } catch (error) {
        console.error('Error removing design:', error);
    }
};

  const addToBasket = async () => {
    const payload = {
        name: props.recordName,
        quantity: 1
    };
    console.log('Sending payload:', payload);
    await axios.post('/api/basket', payload);
    emitter.emit('basketUpdated');
};
  </script>
  
<style scoped>
  .product-container {
      position: relative;
      overflow: hidden;
  }
  
  .product-card {
      position: relative;
      flex: 1 1 300px;
      margin: 3px;
      display: flex;
      flex-direction: column;
      border: 2px solid #050404;
      border-radius: 8px;
      padding: 10px;
      text-align: center;
      height: 300px;
      box-sizing: border-box;
      overflow: hidden; 
      background-color: white;
  }
  
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
  