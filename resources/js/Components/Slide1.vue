<template>
    <div class="p-6 max-w-[400px] mx-auto bg-white rounded-xl space-y-6">
        <div>
            <select
                name="model"
                id="model"
                v-model="selectedModel"
                @change="emitSelectedModel"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
                <option value="" disabled selected>Wybierz model 3D z listy</option>
                <option value="mug">Kubek</option>
                <option value="tshirt">T-shirt</option>
                <!-- <option value="cap">Czapka z daszkiem</option> -->
            </select>
        </div>
  
        <div>
            <input
                type="text"
                id="description"
                v-model="description"
                placeholder="Wpisz opis zdjęcia w języku angielskim"
                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
            />
        </div>
  
        <div class="text-center mt-6">
          <button
            @click="generateImage"
            :disabled="isLoading"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isLoading ? 'Generowanie...' : 'Generuj obraz' }}
          </button>
        </div>
  
        <div class="mt-6">
          <div class="flex justify-center">
            <!-- Loading spinner -->
            <div v-if="isLoading" class="flex items-center justify-center w-[350px] h-[350px] border rounded-md">
              <svg class="animate-spin h-12 w-12 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
            <!-- Generated image -->
            <img v-if="generatedImage" @change="emitGeneratedImage" :src="generatedImage" alt="Generated Image" class="border rounded-md shadow-md max-w-[350px] h-auto" />
          </div>
        </div>
    </div>
</template>
  
<script setup>
import { ref } from 'vue';
  
const selectedModel = ref('');
const description = ref('');
const generatedImage = ref(null);
const isWarningModalVisible = ref(false);
const isLoading = ref(false);
  
const emit = defineEmits(['update-model','generated-image','update-description']);
  
const emitSelectedModel = () => {
    emit('update-model', selectedModel.value);
};
const emitGeneratedImage = () => {
    emit('generated-image',generatedImage.value);
};

const generateImage = async () => {
  if (!description.value.trim()) {
      alert('Proszę wpisać opis obrazu');
      return;
  }

  isLoading.value = true;
  generatedImage.value = null;

  try {
      const response = await fetch('http://127.0.0.1:7860/sdapi/v1/txt2img', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
          },
          body: JSON.stringify({
              prompt: description.value,
              negative_prompt: "low quality, blurry",
              steps: 30,
              cfg_scale: 7.5,
              width: 512,
              height: 512,
              sampler_name: "Euler a",
          }),
      });

      if (!response.ok) {
          throw new Error('Nie udało się wygenerować obrazu');
      }

      const data = await response.json();
      generatedImage.value = `data:image/png;base64,${data.images[0]}`;
      emit('generated-image', generatedImage.value);
      emit('update-description', description.value);
  } catch (error) {
      console.error(error);
      alert('Wystąpił błąd podczas generowania obrazu.');
  } finally {
      isLoading.value = false;
  }
};

</script>
  
<style scoped>

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
  padding: 1rem;
  border-radius: 8px;
  width: 90%;
  max-width: 400px;
}
</style>
