<script setup lang="ts">
    import { Head, useForm } from '@inertiajs/vue3';
    import { Button } from '@/components/ui/button'
    import { index, store } from '@/routes/provider/services';

    defineOptions({
        layout: {
            breadcrumbs: [
                {
                    title: 'Services',
                    href: index()
                },

                {
                    title: 'Create',
                    href: '#'
                },
            ],
        },
    });
    

    const form = useForm({
        name: '',
        description: '',
        duration_minutes: 0,
        price: 0,
        is_active: true,
    });

    function submit() {
        form.post(store().url);
    }
</script>

<template>
    <Head title="Create Service" />
    <div class="p-8">
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label for="Name" class="block mb-2.5 text-sm font-medium text-heading">Name</label>
                <input v-model="form.name" type="text" id="name" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-2.5 py-2 shadow-xs placeholder:text-body" placeholder="" />
                <p v-if="form.errors.name" class="text-sm text-red-500 mt-0.5">{{ form.errors.name }}</p>
            </div>

            <div class="mb-4">
                <label for="description" class="block mb-2.5 text-sm font-medium text-heading">description</label>
                <textarea v-model="form.description" id="description" rows="4" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-3.5 shadow-xs placeholder:text-body" placeholder="Write your thoughts here..."></textarea>
                <p v-if="form.errors.description" class="text-sm text-red-500 mt-0.5">{{ form.errors.description }}</p>
            </div>

            <div class="mb-4">
                <label for="duration_minutes" class="block mb-2.5 text-sm font-medium text-heading">Duration (minutes)</label>
                <input v-model="form.duration_minutes" type="number" id="duration_minutes" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-2.5 py-2 shadow-xs placeholder:text-body" placeholder="" />
                <p v-if="form.errors.duration_minutes" class="text-sm text-red-500 mt-0.5">{{ form.errors.duration_minutes }}</p>
            </div>

            <div class="mb-4">
                <label for="price" class="block mb-2.5 text-sm font-medium text-heading">Price</label>
                <input v-model="form.price" type="number" step="0.01" id="price" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-2.5 py-2 shadow-xs placeholder:text-body" placeholder="" />
                <p v-if="form.errors.price" class="text-sm text-red-500 mt-0.5">{{ form.errors.price }}</p>
            </div>

            <div class="mb-4 flex items-center">
                <label for="is_active" class="block mb-2.5 text-sm font-medium text-heading mr-4">{{  form.is_active ? 'Active' : 'Inactive'  }}</label>
                <input v-model="form.is_active" type="checkbox" id="is_active" class="form-checkbox text-brand border-default-medium rounded-base focus:ring-brand mb-2" />
                <p v-if="form.errors.is_active" class="text-sm text-red-500 mt-0.5">{{ form.errors.is_active }}</p>
            </div>  
            <Button type="submit" class="mb-5">
                Create Service
            </Button>    
        </form>
    </div>
</template>
