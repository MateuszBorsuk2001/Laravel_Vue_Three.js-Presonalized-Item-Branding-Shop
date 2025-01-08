<template>
    <div class="relative bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Carousel -->
        <div class="carousel">
            <div 
                class="carousel-inner" 
            >
            <div v-if="carousel.currentIndex === 0" class="carousel-item p-8 text-center ">
                <Slide1 @update-model="asignModel" @generated-image="asignImage" />
              </div>
              <!-- Slide 2 - 3D model customization -->
              <div v-if="carousel.currentIndex === 1" class="carousel-item p-8 text-center">
                <Slide2 :model="selectedModel" :generatedImage="generatedImage" @saveModel="handleSaveModel" @modelScreenshot="handleScreenshot" />
              </div>
   
              <!-- Slide 3 - Payment -->
              <div v-if="carousel.currentIndex === 2" class="carousel-item p-8 text-center">
                <Slide3 :modelScreenshot="modelScreenshot" />
      
              </div>
            </div>
            <!-- Navigation arrows -->
            <button 
                class="absolute top-1/2 left-0 transform -translate-y-1/2 p-2 disabled:opacity-50" 
                @click="prevSlide"
                :disabled="carousel.currentIndex === 0"
                aria-label="Previous Slide"
            >
            <svg fill="#000000" height="30px" width="30px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 404.258 404.258" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <polygon points="289.927,18 265.927,0 114.331,202.129 265.927,404.258 289.927,386.258 151.831,202.129 "></polygon> </g></svg>

            </button>
            <button 
                class="absolute top-1/2 right-0 transform -translate-y-1/2 text-white p-2 disabled:opacity-50" 
                @click="nextSlide"
                :disabled="carousel.currentIndex === carousel.slides.length - 1"
                aria-label="Next Slide"
            >
            <svg fill="#000000" height="30px" width="30px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 330.002 330.002" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path id="XMLID_226_" d="M233.252,155.997L120.752,6.001c-4.972-6.628-14.372-7.97-21-3c-6.628,4.971-7.971,14.373-3,21 l105.75,140.997L96.752,306.001c-4.971,6.627-3.627,16.03,3,21c2.698,2.024,5.856,3.001,8.988,3.001 c4.561,0,9.065-2.072,12.012-6.001l112.5-150.004C237.252,168.664,237.252,161.33,233.252,155.997z"></path> </g></svg>
            </button>

            <!-- Carousel indicators -->
            <div class="absolute bottom-0 left-0 right-0 p-4 flex justify-center space-x-2">
                <span 
                    v-for="(slide, index) in carousel.slides" 
                    :key="index" 
                    class="w-3 h-3 rounded-full cursor-pointer"
                    :class="{
                        'bg-gray-800': index === carousel.currentIndex,
                        'bg-gray-400': index !== carousel.currentIndex
                    }"
                ></span>
            </div>
        </div>
        <div v-if="showWarningModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-8 max-w-sm mx-4">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Wybierz model</h3>
                    <p class="mt-2 text-sm text-gray-500">Proszę wybrać model 3D z listy przed przejściem dalej.</p>
                    <button 
                        @click="showWarningModal = false"
                        class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none"
                    >
                        Rozumiem
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { reactive, ref, computed } from 'vue';
import Slide1 from '@/Components/Slide1.vue';
import Slide2 from '@/Components/Slide2.vue';
import Slide3 from '@/Components/Slide3.vue';
import axios from 'axios';

const selectedModel = ref('');
let generatedImage = ref('');
let savedModel = ref('');
let modelScreenshot = ref('');
const showWarningModal = ref(false);
const handleScreenshot = (screenshot) => {
    modelScreenshot.value = screenshot;
}

const checkAuth = async () => {
    try {
        // Get CSRF token and establish session
        await axios.get('/sanctum/csrf-cookie');
        
        // Add session cookie to subsequent requests
        const authStatus = await axios.get('/api/auth-status', {
            withCredentials: true,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Referer': window.location.origin
            }
        });
        console.log('Auth status:', authStatus.data);
        
    } catch (error) {
        console.log('Error details:', error.response?.data);
    }
};


const handleSaveModel = async (modelData) => {
    try {
        await axios.get('/sanctum/csrf-cookie');
        await checkAuth();
        const token = document.cookie
            .split('; ')
            .find(row => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];

        const processedData = {
            name: `${selectedModel.value}-${Date.now()}`,
            model: selectedModel.value,
            logos: modelData.logos.map(logo => ({
                texture: logo.texture,
                position: logo.position,
                size: logo.size,
                rotation: logo.rotation
            }))
        };

        const response = await axios.post('http://localhost:8000/api/items', processedData, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            withCredentials: true  // Required for Sanctum
        });
        
        console.log('Model saved successfully:', response.data);
        savedModel.value = response.data;
        
    } catch (error) {
        console.error('Error saving model:', error);
    }
};

function asignModel(model) {
    selectedModel.value = model;
};

function asignImage(image) {
    generatedImage = image;
};
// Carousel state
const carousel = reactive({
    slides: [
        { id: 1, header: 'Slide 1 - Prześlij grafikę i wybierz model 3D'},
        { id: 2, header: 'Slide 2 - Ustaw grafikę na modelu 3D'},
        { id: 3, header: 'Slide 3 - Płatność'}
    ],
    currentIndex: 0
});

// Carousel navigation functions
const nextSlide = () => {
    if (carousel.currentIndex === 0 && !selectedModel.value) {
        showWarningModal.value = true;
        return;
    }
    if (carousel.currentIndex < carousel.slides.length - 1) {
        carousel.currentIndex++;
    }
};

const prevSlide = () => {
    if (carousel.currentIndex > 0) {
        carousel.currentIndex--;
    }
};
</script>
<style scoped>
.btn1:hover {
      transform: rotate(180deg) scale(1.2); 
}
button:not([disabled]):hover svg {
    transform: scale(1.2); 
}

.carousel {
    overflow: hidden;
    position: relative;
}

.carousel-inner {
    display: flex;
    transition: transform 0.3s ease-in-out;
}

.carousel-item {
    flex: 0 0 100%;
    height: 720px;
}
</style>
