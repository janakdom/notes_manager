<script setup lang="ts">
    import { onMounted } from 'vue'

    import BasicLayout from "../layouts/BasicLayout.vue";
    import NotesList from "../components/NotesList.vue";
    import NotesFilter from "../components/NotesFilter.vue";
    import NoteForm from "../components/NoteForm.vue";
    import AddNoteButton from "../components/AddNoteButton.vue";
    import { useNotes } from '../composables/useNotes'

    const {
        notes, isLoading, errorMessage, priorityFilter,
        editingNote, showForm, formError,
        loadNotes, setPriorityFilter,
        startCreating, startEditing, cancelForm, saveNote,
    } = useNotes()

    onMounted(() => {
        void loadNotes()
    })
</script>

<template>
    <BasicLayout
        title="Moje poznámky"
        description="Jednoduchý přehled všech poznámek."
    >
        <div class="toolbar">
            <NotesFilter
                :model-value="priorityFilter"
                @update:model-value="setPriorityFilter"
            />
            <AddNoteButton v-if="!showForm" @click="startCreating" />
        </div>

        <NoteForm
            v-if="showForm"
            :note="editingNote"
            @submit="saveNote"
            @cancel="cancelForm"
        />

        <p v-if="formError" class="form-error">{{ formError }}</p>

        <NotesList
            :notes="notes"
            :is-loading="isLoading"
            :error-message="errorMessage"
            @edit="startEditing"
        />
    </BasicLayout>
</template>

<style scoped>
    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 24px;
    }

    .form-error {
        margin: -16px 0 16px;
        padding: 12px;
        border: 1px solid #f3b3b3;
        border-radius: 8px;
        background: #fff4f4;
        color: #b42318;
        font-size: 0.9rem;
    }
</style>
