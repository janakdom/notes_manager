import { ref } from 'vue'

import { fetchNotes, type Note } from '../api/notesApi'

export function useNotes() {
    const notes = ref<Note[]>([])
    const isLoading = ref(false)
    const errorMessage = ref('')

    /**
     * Asynchronously loads notes by fetching them and updates the application state.
     *
     * @return {Promise<void>} A promise that resolves when the notes have been successfully loaded or an error has been handled.
     */
    async function loadNotes(): Promise<void> {
        isLoading.value = true
        errorMessage.value = ''

        try {
            notes.value = await fetchNotes()
        } catch (error) {
            errorMessage.value = error instanceof Error
                ? error.message
                : 'Nepodarilo se nacist poznamky.'
        } finally {
            isLoading.value = false
        }
    }

    return {
        notes,
        isLoading,
        errorMessage,
        loadNotes,
    }
}