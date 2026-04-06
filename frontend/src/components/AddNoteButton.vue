<script setup lang="ts">
const emit = defineEmits<{
	click: []
}>()
</script>

<template>
	<button class="btn-add" @click="emit('click')">
		Nová poznámka
	</button>
</template>

<style scoped>
	.btn-add {
		padding: 8px 20px;
		border: none;
		border-radius: 8px;
		background: #0f172a;
		color: #ffffff;
		font-size: 0.9rem;
		cursor: pointer;
		white-space: nowrap;
	}
</style>
