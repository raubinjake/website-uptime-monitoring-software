<script setup>
import { computed, onMounted, ref } from 'vue';

const clients = ref([]);
const selectedClientId = ref('');
const isLoading = ref(true);
const errorMessage = ref('');
const pendingWebsite = ref(null);

const selectedClient = computed(() => {
    return clients.value.find((client) => String(client.id) === String(selectedClientId.value));
});

const websites = computed(() => selectedClient.value?.websites ?? []);

onMounted(async () => {
    await loadClients();
});

async function loadClients() {
    isLoading.value = true;
    errorMessage.value = '';

    try {
        const response = await fetch('/api/clients');

        if (!response.ok) {
            throw new Error('Unable to load clients.');
        }

        const data = await response.json();
        const currentClientId = selectedClientId.value;

        clients.value = data.clients ?? [];
        selectedClientId.value = clients.value.some((client) => String(client.id) === String(currentClientId))
            ? currentClientId
            : clients.value[0]?.id ?? '';
    } catch (error) {
        errorMessage.value = error.message;
    } finally {
        isLoading.value = false;
    }
}

function requestVisit(website) {
    pendingWebsite.value = website;
}

function continueVisit() {
    if (pendingWebsite.value) {
        window.open(pendingWebsite.value.url, '_blank', 'noopener,noreferrer');
        pendingWebsite.value = null;
    }
}

function formatCheckedAt(value) {
    if (!value) {
        return 'Not checked yet';
    }

    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}
</script>

<template>
    <main class="monitor-shell">
        <section class="monitor-panel">
            <header class="monitor-header">
                <div>
                    <p class="eyebrow">Uptime monitor</p>
                    <h1>Client websites</h1>
                </div>
                <button type="button" class="refresh-button" :disabled="isLoading" @click="loadClients">
                    Refresh
                </button>
            </header>

            <div v-if="isLoading" class="notice">Loading clients...</div>
            <div v-else-if="errorMessage" class="notice error">{{ errorMessage }}</div>

            <template v-else>
                <label class="client-picker">
                    <span>Client email</span>
                    <select v-model="selectedClientId">
                        <option value="" disabled>Select a client</option>
                        <option v-for="client in clients" :key="client.id" :value="client.id">
                            {{ client.email }}
                        </option>
                    </select>
                </label>

                <div v-if="selectedClient" class="summary-row">
                    <div>
                        <span>{{ websites.length }}</span>
                        Active websites
                    </div>
                    <div>
                        <span>{{ selectedClient.email }}</span>
                        Selected client
                    </div>
                </div>

                <ul v-if="websites.length" class="website-list">
                    <li v-for="website in websites" :key="website.id">
                        <div class="website-main">
                            <button type="button" class="website-link" @click="requestVisit(website)">
                                {{ website.url }}
                            </button>
                            <span class="status" :class="website.last_status || 'pending'">
                                {{ website.last_status || 'pending' }}
                            </span>
                        </div>
                        <div class="website-meta">
                            <span>{{ formatCheckedAt(website.last_checked_at) }}</span>
                            <span v-if="website.last_status_code">HTTP {{ website.last_status_code }}</span>
                        </div>
                    </li>
                </ul>

                <p v-else class="notice">No active websites are registered for this client.</p>
            </template>
        </section>

        <dialog :open="Boolean(pendingWebsite)" class="visit-dialog">
            <p v-if="pendingWebsite">You are about to visit {{ pendingWebsite.url }}. Do you want to continue?</p>
            <div class="dialog-actions">
                <button type="button" class="secondary" @click="pendingWebsite = null">Cancel</button>
                <button type="button" @click="continueVisit">Continue</button>
            </div>
        </dialog>
    </main>
</template>
