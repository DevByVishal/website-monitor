<script setup>
import { onMounted, ref, computed } from 'vue'
import axios from 'axios'

const clients = ref([])
const selectedClientId = ref('')
const selectedWebsite = ref(null)
const loading = ref(true)
const error = ref('')

const selectedClient = computed(() => {
    return clients.value.find(
        client => client.id == selectedClientId.value
    )
})

onMounted(async () => {
    try {
        const response = await axios.get('/api/clients')
        clients.value = response.data
    } catch (e) {
        console.error(e)
        error.value = 'Unable to load clients. Please try again.'
    } finally {
        loading.value = false
    }
})

const openConfirmation = (website) => {
    selectedWebsite.value = website
}

const cancelVisit = () => {
    selectedWebsite.value = null
}

const continueToWebsite = () => {
    if (!selectedWebsite.value) {
        return
    }

    window.open(
        selectedWebsite.value.url,
        '_blank',
        'noopener,noreferrer'
    )

    selectedWebsite.value = null
}
</script>

<template>
    <div class="app">

        <!-- Header -->
        <header class="header">
            <div class="header-inner">
                <div class="brand">
                    <div class="brand-icon">
                        ◉
                    </div>

                    <div>
                        <h1>Website Monitor</h1>
                        <p>Client website dashboard</p>
                    </div>
                </div>

                <div class="status">
                    <span class="status-dot"></span>
                    Monitoring
                </div>
            </div>
        </header>

        <!-- Main -->
        <main class="main">

            <div class="page-title">
                <h2>Client Websites</h2>
                <p>
                    Select a client to view their monitored websites.
                </p>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="state-card">
                <div class="spinner"></div>
                <p>Loading clients...</p>
            </div>

            <!-- Error -->
            <div v-else-if="error" class="error-card">
                <div class="error-icon">!</div>
                <div>
                    <strong>Unable to load clients</strong>
                    <p>{{ error }}</p>
                </div>
            </div>

            <template v-else>

                <!-- Client Select -->
                <section class="card">
                    <label for="client">
                        Select Client
                    </label>

                    <div class="select-wrapper">
                        <select
                            id="client"
                            v-model="selectedClientId"
                        >
                            <option value="">
                                Select a client
                            </option>

                            <option
                                v-for="client in clients"
                                :key="client.id"
                                :value="client.id"
                            >
                                {{ client.email }}
                            </option>
                        </select>

                        <span class="select-arrow">⌄</span>
                    </div>
                </section>

                <!-- Websites -->
                <section
                    v-if="selectedClient"
                    class="card websites-card"
                >
                    <div class="section-header">
                        <div>
                            <h3>Websites</h3>
                            <p>
                                {{ selectedClient.websites.length }}
                                monitored website<span
                                    v-if="selectedClient.websites.length !== 1"
                                >s</span>
                            </p>
                        </div>

                        <span class="client-badge">
                            {{ selectedClient.email }}
                        </span>
                    </div>

                    <div
                        v-if="selectedClient.websites.length"
                        class="website-list"
                    >
                        <button
                            v-for="website in selectedClient.websites"
                            :key="website.id"
                            class="website-item"
                            type="button"
                            @click="openConfirmation(website)"
                        >
                            <span class="website-left">
                                <span class="website-icon">
                                    🌐
                                </span>

                                <span class="website-url">
                                    {{ website.url }}
                                </span>
                            </span>

                            <span class="arrow">
                                →
                            </span>
                        </button>
                    </div>

                    <div
                        v-else
                        class="empty-state"
                    >
                        No websites configured for this client.
                    </div>
                </section>

                <!-- Empty selection -->
                <div
                    v-else
                    class="empty-selection"
                >
                    <div class="empty-icon">
                        🌐
                    </div>

                    <h3>Select a client</h3>

                    <p>
                        Choose a client above to see their monitored
                        websites.
                    </p>
                </div>

            </template>
        </main>

        <!-- Confirmation Modal -->
        <div
            v-if="selectedWebsite"
            class="modal-overlay"
            @click.self="cancelVisit"
        >
            <div class="modal">

                <button
                    class="close-button"
                    type="button"
                    @click="cancelVisit"
                >
                    ×
                </button>

                <div class="modal-icon">
                    ↗
                </div>

                <h2>Visit website?</h2>

                <p class="modal-description">
                    You are about to visit:
                </p>

                <div class="website-preview">
                    {{ selectedWebsite.url }}
                </div>

                <p class="modal-description">
                    This website will open in a new browser tab.
                </p>

                <div class="modal-actions">

                    <button
                        type="button"
                        class="cancel-button"
                        @click="cancelVisit"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        class="continue-button"
                        @click="continueToWebsite"
                    >
                        Continue
                        <span>↗</span>
                    </button>

                </div>

            </div>
        </div>

    </div>
</template>