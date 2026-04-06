<script setup lang="ts">
import { reactive } from 'vue'
import type { Note } from '../types/note'
import type { NotePayload } from '../api/notesApi'

interface Props {
	note?: Note | null
}

const props = withDefaults(defineProps<Props>(), { note: null })

const emit = defineEmits<{
	submit: [data: NotePayload]
	cancel: []
}>()

const form = reactive<NotePayload>({
	title: props.note?.title ?? '',
	content: props.note?.content ?? '',
	priority: props.note?.priority ?? 'medium',
})

function handleSubmit() {
	emit('submit', {
		...form,
		title: form.title.trim(),
		content: form.content.trim(),
	})
}
</script>

<template>
	<form class="note-form" @submit.prevent="handleSubmit">
		<div class="form-field">
			<label for="note-title">Název</label>
			<input
				id="note-title"
				v-model="form.title"
				type="text"
				maxlength="255"
				required
			/>
		</div>

		<div class="form-field">
			<label for="note-content">Obsah</label>
			<textarea
				id="note-content"
				v-model="form.content"
				rows="4"
				required
			></textarea>
		</div>

		<div class="form-field">
			<label for="note-priority">Priorita</label>
			<select id="note-priority" v-model="form.priority" required>
				<option value="low">Nízká</option>
				<option value="medium">Střední</option>
				<option value="high">Vysoká</option>
			</select>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-save">
				{{ note ? 'Uložit' : 'Přidat' }}
			</button>
			<button type="button" class="btn btn-cancel" @click="emit('cancel')">
				Zrušit
			</button>
		</div>
	</form>
</template>

<style scoped>
	.note-form {
		padding: 20px;
		border: 1px solid #d8dee8;
		border-radius: 14px;
		background: #ffffff;
		box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
		margin-bottom: 24px;
	}

	.form-field {
		margin-bottom: 16px;
		text-align: left;
	}

	.form-field label {
		display: block;
		margin-bottom: 4px;
		font-size: 0.85rem;
		font-weight: 600;
		color: #334155;
	}

	.form-field input,
	.form-field textarea,
	.form-field select {
		width: 100%;
		padding: 8px 12px;
		border: 1px solid #d8dee8;
		border-radius: 8px;
		font-size: 0.95rem;
		font-family: inherit;
		box-sizing: border-box;
	}

	.form-field textarea {
		resize: vertical;
	}

	.form-actions {
		display: flex;
		gap: 8px;
	}

	.btn {
		padding: 8px 20px;
		border: none;
		border-radius: 8px;
		font-size: 0.9rem;
		cursor: pointer;
	}

	.btn-save {
		background: #0f172a;
		color: #ffffff;
	}

	.btn-cancel {
		background: #e2e8f0;
		color: #334155;
	}
</style>
