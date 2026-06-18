<script setup lang="ts">
    import { Head, Link } from '@inertiajs/vue3';
    import { Button } from '@/components/ui/button'
    import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    } from '@/components/ui/table';
    import { index, create, edit } from '@/routes/provider/schedules';
    import type { Schedule } from '@/types';

    defineProps<{
        schedules: Schedule[];
    }>();

    defineOptions({
        layout: {
            breadcrumbs: [
                {
                    title: 'Schedules',
                    href: index()
                },
            ],
        },
    });
</script>

<template>
    <Head title="Schedules" />
    <div class="p-8">
        <Button as-child class="mb-5">
            <Link :href="create()">Add New Schedule</Link>
        </Button>
        
        <div>
            <Table class="border border-gray-200 [&_td]:border [&_td]:border-gray-200 [&_th]:border [&_th]:border-gray-200">
                <TableCaption v-if="schedules?.length">A list of schedules.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>Day of Week</TableHead>
                        <TableHead>Start Time</TableHead>
                        <TableHead>End Time</TableHead>
                        <TableHead>Slot Duration in Minutes</TableHead>
                        <TableHead class="text-center">Status</TableHead>
                        <TableHead class="text-center">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="schedules.length">
                        <TableRow v-for="schedule in schedules" :key="schedule.id">
                            <TableCell class="max-w-xs truncate">{{ schedule.day_of_week_label }}</TableCell>
                            <TableCell>{{ schedule.start_time }}</TableCell>
                            <TableCell>{{ schedule.end_time }}</TableCell>
                            <TableCell>{{ schedule.slot_duration_minutes }}</TableCell>
                            <TableCell class="text-center"><span :class="[schedule.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'border px-2 py-1']">{{ schedule.is_active ? 'Active' : 'Inactive' }}</span></TableCell>
                            <TableCell class="text-center">
                                <Button as-child class="bg-blue-500 hover:bg-blue-700 text-white">
                                    <Link :href="edit({schedule: schedule.id})">Edit</Link> 
                                </Button>
                            </TableCell>
                        </TableRow>
                    </template>
                    <TableRow v-else>
                        <TableCell colspan="6" class="text-center py-8 text-muted-foreground">
                            No schedules available.
                            <Link :href="create()" class="underline">Add the first one.</Link>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
    
</template>
