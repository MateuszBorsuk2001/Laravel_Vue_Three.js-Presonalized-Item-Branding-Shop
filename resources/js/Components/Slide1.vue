<template>
    <div>
        <label for="model">Wybierz model 3D:</label>
        <select name="model" id="model" v-model="selectedModel" @change="emitSelectedModel">
            <option value="" disabled selected>Wybierz z listy</option>
            <option value="mug">Mug</option>
            <option value="tshirt">T-shirt</option>
        </select>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const selectedModel = ref('');
const emit = defineEmits(['update-model']);

const emitSelectedModel = () => {
  emit('update-model', selectedModel.value);
};


</script>
