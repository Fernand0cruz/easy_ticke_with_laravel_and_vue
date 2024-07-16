<script setup>
defineProps({
  typeLogo: {
    type: String,
    required: true
  }
});
</script>

<template>
  <img v-if="typeLogo === 'v1'" src="/images/logo/logo.svg" alt="Logo">
  <img v-else src="/images/logo/logo.v2.svg" alt="Logo">
</template>
