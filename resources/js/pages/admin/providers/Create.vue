<script setup lang="ts">
    import { Head, useForm } from '@inertiajs/vue3';
    import { Button } from '@/components/ui/button'
    import { index, store } from '@/routes/admin/providers';

    defineOptions({
        layout: {
            breadcrumbs: [
                {
                    title: 'Providers',
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
        email: '',
        department: '',
        specialization: '',
        bio: '',
        base_fee: '',
        is_active: true,
    });

    function submit() {
        form.post(store().url);
    }
</script>

<template>
    <Head title="Create Provider" />
    <div class="p-8">
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label for="Name" class="block mb-2.5 text-sm font-medium text-heading">Name</label>
                <input v-model="form.name" type="text" id="name" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-2.5 py-2 shadow-xs placeholder:text-body" placeholder="" />
                <p v-if="form.errors.name" class="text-sm text-red-500 mt-0.5">{{ form.errors.name }}</p>
            </div>

            <div class="mb-4">
                <label for="email" class="block mb-2.5 text-sm font-medium text-heading">Email</label>
                <input v-model="form.email" type="email" id="email" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-2.5 py-2 shadow-xs placeholder:text-body" placeholder="" />
                <p v-if="form.errors.email" class="text-sm text-red-500 mt-0.5">{{ form.errors.email }}</p>
            </div>

            <div class="mb-4">
                <label for="department" class="block mb-2.5 text-sm font-medium text-heading">Department</label>
                <input v-model="form.department" type="text" id="department" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-2.5 py-2 shadow-xs placeholder:text-body" placeholder="" />
                <p v-if="form.errors.department" class="text-sm text-red-500 mt-0.5">{{ form.errors.department }}</p>
            </div>

            <div class="mb-4">
                <label for="specialization" class="block mb-2.5 text-sm font-medium text-heading">Specialization</label>
                <input v-model="form.specialization" type="text" id="specialization" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-2.5 py-2 shadow-xs placeholder:text-body" placeholder="" />
                <p v-if="form.errors.specialization" class="text-sm text-red-500 mt-0.5">{{ form.errors.specialization }}</p>
            </div>

            <div class="mb-4">
                <label for="bio" class="block mb-2.5 text-sm font-medium text-heading">Bio</label>
                <textarea v-model="form.bio" id="bio" rows="4" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-3.5 shadow-xs placeholder:text-body" placeholder="Write your thoughts here..."></textarea>
                <p v-if="form.errors.bio" class="text-sm text-red-500 mt-0.5">{{ form.errors.bio }}</p>
            </div>

            <div class="mb-4">
                <label for="base_fee" class="block mb-2.5 text-sm font-medium text-heading">Base Fee</label>
                <input v-model="form.base_fee" type="number" step="0.01" id="base_fee" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-2.5 py-2 shadow-xs placeholder:text-body" placeholder="" />
                <p v-if="form.errors.base_fee" class="text-sm text-red-500 mt-0.5">{{ form.errors.base_fee }}</p>
            </div>

            <div class="mb-4 flex items-center">
                <label for="is_active" class="block mb-2.5 text-sm font-medium text-heading mr-4">{{  form.is_active ? 'Active' : 'Inactive'  }}</label>
                <input v-model="form.is_active" type="checkbox" id="is_active" class="form-checkbox text-brand border-default-medium rounded-base focus:ring-brand mb-2" />
                <p v-if="form.errors.is_active" class="text-sm text-red-500 mt-0.5">{{ form.errors.is_active }}</p>
            </div>  
            <Button type="submit" class="mb-5">
                Create Provider
            </Button>    
        </form>
    </div>
</template>
