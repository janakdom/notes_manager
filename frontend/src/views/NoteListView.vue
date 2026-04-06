<script setup lang="ts">
    import { onMounted } from 'vue'

    import BasicLayout from "../layouts/BasicLayout.vue";
    import NotesList from "../components/NotesList.vue";
    import { useNotes } from '../composables/useNotes'

    const { notes, isLoading, errorMessage, loadNotes } = useNotes()

    onMounted(() => {
        void loadNotes()
    })
</script>

<template>
    <BasicLayout
        title="Moje poznámky"
        description="Jednoduchý přehled všech poznámek."
    >
        <NotesList
            :notes="notes"
            :is-loading="isLoading"
            :error-message="errorMessage"
        />
    </BasicLayout>
</template>
