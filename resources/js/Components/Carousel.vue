<template>
    <div class="relative bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Carousel -->
        <div class="carousel">
            <div 
                class="carousel-inner" 
            >
            <div v-if="carousel.currentIndex === 0" class="carousel-item p-8 text-center ">
                <Slide1 />
              </div>

              <!-- Slide 2 - 3D model customization -->
              <div v-if="carousel.currentIndex === 1" class="carousel-item p-8 text-center">
                <Slide2 />
              </div>

              <!-- Slide 3 - Payment -->
              <div v-if="carousel.currentIndex === 2" class="carousel-item p-8 text-center">
                <Slide3 />
      
              </div>
            </div>
            <!-- Navigation arrows -->
            <button 
                class="absolute top-1/2 left-0 transform -translate-y-1/2 p-2 disabled:opacity-50" 
                @click="prevSlide"
                :disabled="carousel.currentIndex === 0"
                aria-label="Previous Slide"
            >
            <svg class="m-[-5px] btn1" fill="#000000" height="30px" width="30px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 330 330" xml:space="preserve" transform="rotate(180)">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path id="XMLID_222_" d="M250.606,154.389l-150-149.996c-5.857-5.858-15.355-5.858-21.213,0.001 c-5.857,5.858-5.857,15.355,0.001,21.213l139.393,139.39L79.393,304.394c-5.857,5.858-5.857,15.355,0.001,21.213 C82.322,328.536,86.161,330,90,330s7.678-1.464,10.607-4.394l149.999-150.004c2.814-2.813,4.394-6.628,4.394-10.606 C255,161.018,253.42,157.202,250.606,154.389z"></path>
                    </g>
            </svg>

            </button>
            <button 
                class="absolute top-1/2 right-0 transform -translate-y-1/2 text-white p-2 disabled:opacity-50" 
                @click="nextSlide"
                :disabled="carousel.currentIndex === carousel.slides.length - 1"
                aria-label="Next Slide"
            >
            <svg class="m-[-5px]" fill="#000000" height="30px" width="30px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 330 330" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path id="XMLID_222_" d="M250.606,154.389l-150-149.996c-5.857-5.858-15.355-5.858-21.213,0.001 c-5.857,5.858-5.857,15.355,0.001,21.213l139.393,139.39L79.393,304.394c-5.857,5.858-5.857,15.355,0.001,21.213 C82.322,328.536,86.161,330,90,330s7.678-1.464,10.607-4.394l149.999-150.004c2.814-2.813,4.394-6.628,4.394-10.606 C255,161.018,253.42,157.202,250.606,154.389z"></path> </g></svg>
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
                    @click="carousel.currentIndex = index"
                ></span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue';
import Slide1 from '@/Components/Slide1.vue';
import Slide2 from '@/Components/Slide2.vue';
import Slide3 from '@/Components/Slide3.vue';

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
