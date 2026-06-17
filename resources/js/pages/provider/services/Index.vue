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
    import { index, create, edit } from '@/routes/provider/services';
    import type { Service } from '@/types';

    defineProps<{
        services: Service[];
    }>();

    defineOptions({
        layout: {
            breadcrumbs: [
                {
                    title: 'Services',
                    href: index()
                },
            ],
        },
    });
</script>

<template>
    <Head title="Services" />
    <div class="p-8">
        <Button as-child class="mb-5">
            <Link :href="create()">Add New Service</Link>
        </Button>
        
        <div>
            <Table class="border border-gray-200 [&_td]:border [&_td]:border-gray-200 [&_th]:border [&_th]:border-gray-200">
                <TableCaption v-if="services?.length">A list of services.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-25">Name</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead>Duration in Minutes</TableHead>
                        <TableHead>Price</TableHead>
                        <TableHead class="text-center">Status</TableHead>  
                        <TableHead class="text-center">Actions</TableHead>      
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="services.length">
                        <TableRow v-for="service in services" :key="service.id">
                            <TableCell class="font-medium">
                                {{ service.name }}
                            </TableCell>
                            <TableCell class="max-w-xs truncate">{{ service.description }}</TableCell>
                            <TableCell>{{ service.duration_minutes }}</TableCell>
                            <TableCell>Rs. {{ Number(service.price).toFixed(2) }}</TableCell>
                            <TableCell class="text-center"><span :class="[service.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'border px-2 py-1']">{{ service.is_active ? 'Active' : 'Inactive' }}</span></TableCell>
                            <TableCell class="text-center">
                                <Button as-child class="bg-blue-500 hover:bg-blue-700 text-white">
                                    <Link :href="edit({service: service.id})">Edit</Link> 
                                </Button>
                            </TableCell>
                        </TableRow>
                    </template>
                    <TableRow v-else>
                        <TableCell colspan="6" class="text-center py-8 text-muted-foreground">
                            No services available.
                            <Link :href="create()" class="underline">Add the first one.</Link>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
    
</template>
