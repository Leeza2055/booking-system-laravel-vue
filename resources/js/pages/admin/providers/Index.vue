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
    import { index, create, edit } from '@/routes/admin/providers';
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
        <Button as-child class="mb-5">
            <Link :href="create()">Add New Provider</Link>
        </Button>
        
        <div>
            <Table class="border border-gray-200 [&_td]:border [&_td]:border-gray-200 [&_th]:border [&_th]:border-gray-200">
                <TableCaption>A list of providers.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-25">
                        Provider Name
                        </TableHead>
                        <TableHead>Department</TableHead>
                        <TableHead>Specialization</TableHead>
                        <TableHead>Bio</TableHead>
                        <TableHead>Base Fee</TableHead>
                        <TableHead class="text-center">Status</TableHead>  
                        <TableHead class="text-center">Actions</TableHead>      
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="provider in providers" :key="provider.id">
                        <TableCell class="font-medium">
                            {{ provider.user.name }}
                        </TableCell>
                        <TableCell>{{ provider.department }}</TableCell>
                        <TableCell>{{ provider.specialization }}</TableCell>
                        <TableCell class="max-w-xs truncate">{{ provider.bio ?? '—' }}</TableCell>
                        <TableCell>Rs. {{ Number(provider.base_fee).toFixed(2) }}</TableCell>
                        <TableCell class="text-center"><span :class="[provider.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'border px-2 py-1']">{{ provider.is_active ? 'Active' : 'Inactive' }}</span></TableCell>
                        <TableCell class="text-center">
                            <Button as-child class="bg-blue-500 hover:bg-blue-700 text-white">
                                <Link :href="edit({provider: provider.id})">Edit</Link> 
                            </Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
    
</template>
