<template>
    <div>
        <label for="upload">Wybierz grafikę:</label>
        <input type="file" id="upload" @change="handleFileUpload">
        <br/>
        <label for="model">Wybierz model 3D:</label>
        <select name="model" id="model">
            <option value="" disabled selected>Wybierz model 3D</option>
            <option value="mug">Mug</option>
            <option value="model2">Model 2</option>
            <option value="model3">Model 3</option>
        </select>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const uploadedImage = ref(null);
const selectedModel = ref('mug');

// Funkcja do obsługi przesyłania plików
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            uploadedImage.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};
</script>
