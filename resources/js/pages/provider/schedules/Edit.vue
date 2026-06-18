<script setup lang="ts">
    import { Head, useForm } from '@inertiajs/vue3';
    import { Button } from '@/components/ui/button'
    import { index, update } from '@/routes/provider/schedules';
    import type { Schedule } from '@/types';

    const props = defineProps<{ schedule: Schedule }>();

    defineOptions({
        layout: {
            breadcrumbs: [
                {
                    title: 'Schedules',
                    href: index()
                },

                {
                    title: 'Edit',
                    href: '#'
                },
            ],
        },
    });
    
    const form = useForm({
        day_of_week: props.schedule.day_of_week,
        start_time: props.schedule.start_time,
        end_time: props.schedule.end_time,
        slot_duration_minutes: props.schedule.slot_duration_minutes,
        is_active: Boolean(props.schedule.is_active),
    });

    function submit() {
        form.put(update({schedule: props.schedule.id}).url);
    }
</script>

<template>
    <Head title="Edit Schedule" />
    <div class="p-8">
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label for="day_of_week" class="block mb-2.5 text-sm font-medium text-heading">Day of Week</label>
                <select v-model="form.day_of_week" id="day_of_week" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-2.5 py-2 shadow-xs placeholder:text-body">
                    <option value="">Select a day</option>
                    <option :value="0">Sunday</option>
                    <option :value="1">Monday</option>
                    <option :value="2">Tuesday</option>
                    <option :value="3">Wednesday</option>
                    <option :value="4">Thursday</option>
                    <option :value="5">Friday</option>
                    <option :value="6">Saturday</option>
                </select>
                <p v-if="form.errors.day_of_week" class="text-sm text-red-500 mt-0.5">{{ form.errors.day_of_week }}</p>
            </div>

            <div class="mb-4">
                <label for="start_time" class="block mb-2.5 text-sm font-medium text-heading">Start Time</label>
                <input v-model="form.start_time" type="time" id="start_time" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-2.5 py-2 shadow-xs placeholder:text-body" />
                <p v-if="form.errors.start_time" class="text-sm text-red-500 mt-0.5">{{ form.errors.start_time }}</p>
            </div>

            <div class="mb-4">
                <label for="end_time" class="block mb-2.5 text-sm font-medium text-heading">End Time</label>
                <input v-model="form.end_time" type="time" id="end_time" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-2.5 py-2 shadow-xs placeholder:text-body" />
                <p v-if="form.errors.end_time" class="text-sm text-red-500 mt-0.5">{{ form.errors.end_time }}</p>
            </div>

            <div class="mb-4">
                <label for="slot_duration_minutes" class="block mb-2.5 text-sm font-medium text-heading">Slot Duration (minutes)</label>
                <input v-model="form.slot_duration_minutes" type="number" step="1" id="slot_duration_minutes" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-2.5 py-2 shadow-xs placeholder:text-body" placeholder="" />
                <p v-if="form.errors.slot_duration_minutes" class="text-sm text-red-500 mt-0.5">{{ form.errors.slot_duration_minutes }}</p>
            </div>

            <div class="mb-4 flex items-center">
                <label for="is_active" class="block mb-2.5 text-sm font-medium text-heading mr-4">{{  form.is_active ? 'Active' : 'Inactive'  }}</label>
                <input v-model="form.is_active" type="checkbox" id="is_active" class="form-checkbox text-brand border-default-medium rounded-base focus:ring-brand mb-2" />
                <p v-if="form.errors.is_active" class="text-sm text-red-500 mt-0.5">{{ form.errors.is_active }}</p>
            </div>  
            <Button type="submit" class="mb-5">
                Edit Schedule
            </Button>    
        </form>
    </div>
</template>
