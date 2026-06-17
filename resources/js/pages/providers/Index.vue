<script setup lang="ts">
    import { Head, Link } from '@inertiajs/vue3';
    import { index, show } from '@/routes/providers';
    import type { Provider } from '@/types';

    defineProps<{
        providers: Provider[];
    }>();

    defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Providers',
                href: index()
            },
        ],
    },
});
</script>

<template>
    <Head title="Providers" />
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-6">Our Providers</h1>
        <div v-if="providers.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <Link v-for="provider in providers" :key="provider.id" :href="show(provider.id)" class="border rounded-lg p-4 shadow-sm">
                <h2 class="text-lg font-semibold">{{ provider.user.name }}</h2>
                <p class="text-sm text-gray-500">{{ provider.department }}</p>
                <p class="text-sm">{{ provider.specialization }}</p>
                <p class="text-sm font-medium mt-2">From Rs. {{ provider.base_fee }}</p>
            </Link>
        </div>

        <div v-else class="text-center py-16 text-muted-foreground">
            <p class="text-lg">No providers available yet.</p>
            <p class="text-sm mt-1">Check back soon.</p>
        </div>
    </div>
</template>
