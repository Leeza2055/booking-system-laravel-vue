<script setup lang="ts">
    import { Head } from '@inertiajs/vue3';
    import { computed } from 'vue';
    import { index } from '@/routes/providers';
    import type { Provider } from '@/types';

    const props = defineProps<{
        provider: Provider;
    }>();

    const pageTitle = computed(() => `${props.provider.user.name} - Provider Detail`);

    defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Providers',
                href: index(),
            },
            {
                title: 'Provider Detail',
                href: '#',
            },
        ],
    },
});
</script>

<template>
    <Head :title="pageTitle" />
    <div class="p-8">
        <div>
            <h2 class="text-lg font-semibold">{{ provider.user.name }}</h2>
            <p class="text-sm text-gray-500">{{ provider.department }}</p>
            <p class="text-sm">{{ provider.specialization }}</p>
            <p class="text-sm font-medium mt-2">From Rs. {{ provider.base_fee }}</p>
        </div>

        <div class="mt-6">
            <h3 class="text-lg font-semibold mb-3">Services</h3>
            <div v-for="service in provider.services" :key="service.id" class="border rounded p-3 mb-2">
                <p class="font-medium">{{ service.name }}</p>
                <p class="text-sm text-gray-500">{{ service.duration_minutes }} min — Rs. {{ service.price }}</p>
            </div>
        </div>
    </div>
</template>
