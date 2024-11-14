<template>
    <div>
        <h3>Podsumowanie</h3>
        <button @click="proceedToPayment">Przejdź do płatności</button>
    </div>
</template>

<script setup>
const proceedToPayment = () => {
    alert('Przejdź do płatności');
};
</script>
