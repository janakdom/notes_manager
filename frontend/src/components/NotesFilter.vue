<script setup lang="ts">
import type { NotePriority } from '../types/note'

interface FilterOption {
	label: string
	value: NotePriority | null
}

interface Props {
	modelValue: NotePriority | null
}

defineProps<Props>()

const emit = defineEmits<{
	'update:modelValue': [value: NotePriority | null]
}>()

const options: FilterOption[] = [
	{ label: 'Vše', value: null },
	{ label: 'Nízká', value: 'low' },
	{ label: 'Střední', value: 'medium' },
	{ label: 'Vysoká', value: 'high' },
]
</script>

<template>
	<div class="filter">
		<button
			v-for="option in options"
			:key="String(option.value)"
			class="filter-btn"
			:class="{ active: modelValue === option.value }"
			@click="emit('update:modelValue', option.value)"
		>
			{{ option.label }}
		</button>
	</div>
</template>

<style scoped>
	.filter {
		display: flex;
		gap: 8px;
		margin-bottom: 24px;
	}

	.filter-btn {
		padding: 6px 16px;
		border-radius: 999px;
		border: 1px solid #d8dee8;
		background: #ffffff;
		color: #334155;
		font-size: 0.9rem;
		cursor: pointer;
	}

	.filter-btn.active {
		background: #0f172a;
		border-color: #0f172a;
		color: #ffffff;
	}
</style>
