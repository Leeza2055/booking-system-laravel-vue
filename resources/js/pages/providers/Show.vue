<script setup lang="ts">
    import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
    import { computed, ref } from 'vue';
    import { login } from '@/routes';
    import { store } from '@/routes/bookings';
    import { index, slots } from '@/routes/providers';
    import type { Provider } from '@/types';
    

    const props = defineProps<{
        provider: Provider;
    }>();

    const page = usePage();
    const user = computed(() => page.props.auth.user);
    const isCustomer = computed(() => user.value?.role === 'customer');
    const isGuest = computed(() => !user.value);

    const pageTitle = computed(() => `${props.provider.user.name} - Provider Detail`);
    const selectedService = ref<null | number>(null);
    const selectedDate = ref('');
    const selectedSlot = ref<null | string>(null);
    const availableSlots = ref<string[]>([]);
    const loadingSlots = ref(false);
    
    const form = useForm({
        provider_id: props.provider.id,
        service_id: null as number | null,
        booking_date: '',
        start_time: '',
        notes: ''
    });

    async function fetchSlots() {
        if (! selectedService.value || !selectedDate.value) {
            return;
        }

        loadingSlots.value = true;

        try {
            const response = await fetch(slots.url(props.provider.id, { query: { booking_date: selectedDate.value}}));
            const data = await response.json();
            availableSlots.value = data;
            selectedSlot.value = null;
        } catch (error) {
            console.error('Failed to fetch slots:', error);
        } finally {
            loadingSlots.value = false;
        }
    }

    function selectService(serviceId: number) {
        selectedService.value = serviceId;
        form.service_id = serviceId;
        availableSlots.value = [];
        selectedSlot.value = null;
    }

    function selectSlot(slot: string){
        selectedSlot.value = slot;
        form.start_time = slot;
        form.booking_date = selectedDate.value;
    }

    function submit() {
        form.post(store().url);
    }

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
            <h3 class="text-lg font-semibold mb-3">Select a Service</h3>
            <div v-for="service in provider.services" 
                    :key="service.id" 
                    class="border rounded p-3 mb-2 cursor-pointer transition-colors"
                    :class="selectedService === service.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 dark:border-blue-400' 
                        : 'hover:bg-gray-50 dark:hover:bg-gray-800'"
                    @click="selectService(service.id)">
                <p class="font-medium">{{ service.name }}</p>
                <p class="text-sm text-gray-500">{{ service.duration_minutes }} min — Rs. {{ service.price }}</p>
            </div>
        </div>
        
        <div v-if="isCustomer">
            <div v-if="selectedService" class="mt-6">
                <h3 class="text-lg font-semibold mb-3">Select a Date</h3>
                <input type="date" v-model="selectedDate" 
                :min="new Date().toISOString().split('T')[0]"
                @change="fetchSlots" class="border rounded p-2 w-full max-w-xs bg-transparent dark:scheme-dark">
            </div>

            <div v-if="loadingSlots" class="mt-4 text-gray-400">Loading slots...</div>

            <div v-else-if="selectedDate && !loadingSlots && availableSlots.length === 0" class="mt-4 text-gray-400">No slots available for the selected date.</div>

            <div v-else-if="availableSlots.length > 0" class="mt-6">
                <h3 class="text-lg font-semibold mb-3">Select a Time Slot</h3>
                <div class="flex flex-wrap gap-2">
                    <button v-for="slot in availableSlots"
                    :key="slot"
                    type="button"
                    class="border rounded px-3 py-1 text-sm transition-colors"
                    :class="selectedSlot === slot ? 'bg-blue-500 text-white border-blue-500' : 'hover:bg-gray-100 dark:hover:bg-gray-800'"
                    @click="selectSlot(slot)">
                        {{ slot }}
                    </button>
                </div>
            </div>

            <div v-if="selectedSlot" class="mt-6">
                <div>
                    <label class="block mb-2 text-sm font-medium">Notes (optional)</label>
                    <textarea 
                        v-model="form.notes" 
                        rows="3"
                        placeholder="Any special requests or symptoms..."
                        class="border rounded p-2 w-full text-sm bg-transparent"
                    ></textarea>
                </div>
                <p class="text-sm text-gray-500 mb-4">Booking {{ selectedSlot }} with {{ provider.user.name }}</p>
                <button @click="submit" :disabled="form.processing" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-700 disabled:opacity-50">
                    {{ form.processing ? 'Booking...' : 'Confirm Booking' }}
                </button>
            </div>
        </div>

        <div v-else-if="isGuest" class="mt-6">
            <Link :href="login()" class="text-blue-500 underline">Login to book an appointment</Link>
        </div>
    </div>
        
</template>
