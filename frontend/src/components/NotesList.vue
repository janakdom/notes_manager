<script setup lang="ts">
import type { Note } from '../types/note'
import NoteCard from './NoteCard.vue'

interface Props {
	notes: Note[]
	isLoading: boolean
	errorMessage: string
}

defineProps<Props>()
</script>

<template>
	<section class="notes-section">
		<p class="notes-state"
			v-if="isLoading"
        >
			Načítám poznámky...
		</p>

		<p class="notes-state notes-state-error"
			v-else-if="errorMessage"
		>
			{{ errorMessage }}
		</p>

		<p class="notes-state"
			v-else-if="notes.length === 0"
		>
			Zatím tu nejsou žádné poznámky.
		</p>

		<ul class="notes-list"
			v-else
		>
			<li
				v-for="note in notes"
				:key="note.id"
			>
				<NoteCard :note="note" />
			</li>
		</ul>
	</section>
</template>

<style scoped>
	.notes-section {
		min-height: 200px;
	}

	.notes-state {
		margin: 0;
		padding: 24px;
		border: 1px solid #d8dee8;
		border-radius: 12px;
		background: #ffffff;
		color: #334155;
		text-align: left;
	}

	.notes-state-error {
		color: #b42318;
		border-color: #f3b3b3;
		background: #fff4f4;
	}

	.notes-list {
		list-style: none;
		margin: 0;
		padding: 0;
		display: grid;
		gap: 16px;
	}
</style>
