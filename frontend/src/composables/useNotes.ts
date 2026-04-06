import { ref } from 'vue'

import { fetchNotes } from '../api/notesApi'
import type {Note, NotePriority} from "../types/note.ts";

export function useNotes() {
    const notes = ref<Note[]>([])
    const isLoading = ref(false)
    const errorMessage = ref('')
    const priorityFilter = ref<NotePriority | null>(null)

    /**
     * Asynchronously loads notes by fetching them and updates the application state.
     *
     * @return {Promise<void>} A promise that resolves when the notes have been successfully loaded or an error has been handled.
     */
    async function loadNotes(): Promise<void> {
        isLoading.value = true
        errorMessage.value = ''

        try {
            notes.value = await fetchNotes(priorityFilter.value ?? undefined)
        } catch (error) {
            errorMessage.value = error instanceof Error
                ? error.message
                : 'Nepodařilo se načíst poznámky.'
        } finally {
            isLoading.value = false
        }
    }

    /**
     * Sets the priority filter and reloads the notes.
     * @param priority
     */
    async function setPriorityFilter(priority: NotePriority | null): Promise<void> {
        priorityFilter.value = priority
        await loadNotes()
    }

    return {
        notes,
        isLoading,
        errorMessage,
        priorityFilter,
        loadNotes,
        setPriorityFilter,
    }
}