<script setup lang="ts">
    interface Props {
        title: string
        description?: string
    }

    withDefaults(
        defineProps<Props>(),
        { description: '' }
    )
</script>

<template>
    <main class="layout">
        <div class="container">
            <header class="header">
                <h1 class="title">
                    {{ title }}
                </h1>
                <p class="description"
                    v-if="description"
                >
                    {{ description }}
                </p>
            </header>

            <div class="content">
                <slot />
            </div>
        </div>
    </main>
</template>

<style scoped>
    .layout {
        min-height: 100vh;
        padding: 32px 16px;
        background: #f8fafc;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .header {
        margin-bottom: 24px;
    }

    .title {
        margin: 0;
        font-size: 2rem;
        line-height: 1.2;
        color: #0f172a;
    }

    .description {
        font-size: 0.85rem;
        font-style: italic;
        margin-top: 5px;
        line-height: 1.2;
        color: #0f172a;
    }

    .content {
        min-width: 0;
    }
</style>