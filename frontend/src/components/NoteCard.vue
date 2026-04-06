<script setup lang="ts">
import type { Note } from '../types/note'

interface Props {
	note: Note
}

defineProps<Props>()

const priorityLabels: Record<Note['priority'], string> = {
	low: 'Nízká',
	medium: 'Střední',
	high: 'Vysoká',
}
</script>

<template>
	<article class="note-card">
		<div class="note-top">
			<h2 class="note-title">
				{{ note.title }}
			</h2>
			<span class="note-priority">
				{{ priorityLabels[note.priority] }}
			</span>
		</div>

		<p class="note-content">
			{{ note.content }}
		</p>
	</article>
</template>

<style scoped>
	.note-card {
		padding: 20px;
		border: 1px solid #d8dee8;
		border-radius: 14px;
		background: #ffffff;
		box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
		text-align: left;
	}

	.note-top {
		display: flex;
		align-items: flex-start;
		justify-content: space-between;
		gap: 12px;
		margin-bottom: 12px;
	}

	.note-title {
		margin: 0;
		font-size: 1.2rem;
		line-height: 1.3;
		color: #0f172a;
	}

	.note-priority {
		padding: 4px 10px;
		border-radius: 999px;
		background: #e2e8f0;
		color: #334155;
		font-size: 0.8rem;
		font-weight: 600;
	}

	.note-content {
		margin: 0;
		line-height: 1.5;
		white-space: pre-wrap;
	}

	@media (max-width: 640px) {
		.note-top {
			flex-direction: column;
		}
	}
</style>
