import { ref } from 'vue'

import { fetchNotes, createNote, updateNote } from '../api/notesApi'
import type { NotePayload } from '../api/notesApi'
import type { Note, NotePriority } from '../types/note'

export function useNotes() {
    const notes = ref<Note[]>([])
    const isLoading = ref(false)
    const errorMessage = ref('')
    const priorityFilter = ref<NotePriority | null>(null)
    const editingNote = ref<Note | null>(null)
    const showForm = ref(false)
    const formError = ref('')

    /**
     * Asynchronously loads notes by fetching them and updates the application state.
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

    /**
     * Starts the process of creating a new note
     */
    function startCreating() {
        editingNote.value = null
        formError.value = ''
        showForm.value = true
    }

    /**
     * Starts the process of editing an existing note
     */
    function startEditing(note: Note) {
        editingNote.value = note
        formError.value = ''
        showForm.value = true
    }

    /**
     * Cancels the form and resets the editing state
     */
    function cancelForm() {
        showForm.value = false
        editingNote.value = null
        formError.value = ''
    }

    /**
     * Saves the note to the database
     */
    async function saveNote(data: NotePayload): Promise<void> {
        formError.value = ''
        try {
            if (editingNote.value) {
                await updateNote(editingNote.value.id, data)
            } else {
                await createNote(data)
            }
            showForm.value = false
            editingNote.value = null
            await loadNotes()
        } catch (error) {
            formError.value = error instanceof Error
                ? error.message
                : 'Nepodařilo se uložit poznámku.'
        }
    }

    return {
        notes,
        isLoading,
        errorMessage,
        priorityFilter,
        editingNote,
        showForm,
        formError,
        loadNotes,
        setPriorityFilter,
        startCreating,
        startEditing,
        cancelForm,
        saveNote,
    }
}