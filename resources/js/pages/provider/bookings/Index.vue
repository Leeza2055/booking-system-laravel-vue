<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
 import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    } from '@/components/ui/table';
import { useTimeFormat } from '@/composables/useTimeFormat'
import { index } from '@/routes/bookings';
import type { Booking } from '@/types';

const { formatTime } = useTimeFormat()

defineProps<{
    bookings: Booking[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Bookings',
                href: index()
            },
        ],
    },
});

</script>

<template>
    <Head title="Bookings" />
    <div class="p-8">
       <Table class="border border-gray-200 [&_td]:border [&_td]:border-gray-200 [&_th]:border [&_th]:border-gray-200">
                <TableCaption v-if="bookings.length">A list of your bookings.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-25">
                        Customer Name
                        </TableHead>
                        <TableHead>Service</TableHead>
                        <TableHead>Booking Date</TableHead>
                        <TableHead>Start Time</TableHead>
                        <TableHead>End Time</TableHead>
                        <TableHead>Notes</TableHead>
                        <TableHead class="text-center">Status</TableHead>  
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="bookings.length">
                    <TableRow v-for="booking in bookings" :key="booking.id">
                        <TableCell class="font-medium">
                            {{ booking.customer.name }}
                        </TableCell>
                        <TableCell>{{ booking.service.name }}</TableCell>
                        <TableCell>{{ booking.booking_date }}</TableCell>
                        <TableCell>{{ formatTime(booking.start_time) }}</TableCell>
                        <TableCell>{{ formatTime(booking.end_time) }}</TableCell>
                        <TableCell class="max-w-xs truncate">{{ booking.notes ?? '—' }}</TableCell>
                        <TableCell class="text-center">
                            <span :class="{
                                'bg-yellow-100 text-yellow-800': booking.status === 'pending',
                                'bg-green-100 text-green-800': booking.status === 'confirmed',
                                'bg-blue-100 text-blue-800': booking.status === 'completed',
                                'bg-red-100 text-red-800': booking.status === 'cancelled',
                            }" class="border px-2 py-1 capitalize">
                                {{ booking.status }}
                            </span>
                        </TableCell>
                    </TableRow>
                    </template>
                    <TableRow v-else>
                        <TableCell colspan="7" class="text-center py-8 text-muted-foreground">
                            No bookings found. 
                        </TableCell>
                    </TableRow> 
                </TableBody>
            </Table>
    </div>
</template>
