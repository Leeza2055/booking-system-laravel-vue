<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, FolderGit2, LayoutGrid, Briefcase, Calendar } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as adminProvidersIndex } from '@/routes/admin/providers';
import { index as bookingsIndex } from '@/routes/bookings';
import { index as providerBookingIndex } from '@/routes/provider/bookings';
import { index as schedulesIndex } from '@/routes/provider/schedules';
import { index as servicesIndex } from '@/routes/provider/services';
import { index } from '@/routes/providers';
import type { NavItem } from '@/types';
import type { User } from '@/types';

const page = usePage()
const user = computed(() => page.props.auth.user as User | null)

const mainNavItems = computed((): NavItem[] =>{
    const items: NavItem[]  = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ]

    if (user.value?.role === 'customer') {
        items.push({
            title: 'Providers',
            href: index(),
            icon: BookOpen,
        })

        items.push({
            title: 'Bookings',
            href: bookingsIndex(),
            icon: BookOpen,
        })
    }

    if (user.value?.role === 'provider') {
        items.push({
            title: 'Services',
            href: servicesIndex(),
            icon: Briefcase,
        })
        items.push({
            title: 'Schedules',
            href: schedulesIndex(),
            icon: Calendar,
        })
        items.push({
            title: 'Bookings',
            href: providerBookingIndex(),
            icon: BookOpen,
        })
    }

    if (user.value?.role === 'admin') {
        items.push({
            title: 'Providers',
            href: adminProvidersIndex(),
            icon: BookOpen,
        })
    }

    return items

});

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
