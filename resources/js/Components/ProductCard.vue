<template>
    <div class="product-container" @click="navigateToEditor">
    <div class="product-card">
      <img :src="image" alt="Product Image" class="product-image" />
      <div class="product-description">
        <h2>{{ name }}</h2>
        <p>{{ description }}</p>
      </div>
    </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  import { router } from '@inertiajs/vue3';

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
  });

  const navigateToEditor = () => {
    const params = new URLSearchParams();
    params.append('screenshot_path', props.image);
    window.location.href = `/edit?${params.toString()}`;
};
 
  </script>
  
  <style scoped>
  .product-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
  }
  
  .product-card {
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
  
  .product-image {
    width: 100%;
    height: 200px;
    object-fit: cover; 
  }
  
  .product-description {
    flex-grow: 1; 
  }
  
  .product-description h2 {
    font-size: 1.2em;
    margin: 10px 0;
  }
  
  .product-description p {
    font-size: 1em;
    color: #555;
  }
  </style>
  