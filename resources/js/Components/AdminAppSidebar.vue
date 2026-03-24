<script setup lang="ts">
import { defineProps } from "vue";
import {
    LayoutDashboard,
    Building2,
    Users,
    ReceiptText,
    Handshake,
    UserCog,
    ShieldCheck,
    Layers,
    FileQuestion,
    Star,
    Settings2, type LucideIcon,
    CreditCard,
    PackagePlus,
} from "lucide-vue-next"
import NavMain from './NavMain.vue'
import NavUser from './NavUser.vue'
import NyifeLogo from "../../images/nyifeBrand.png"

import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarRail,
} from '../Components/ui/sidebar'

import { computed } from 'vue'
import { Link } from "@inertiajs/vue3";

type SidebarProps = {
    user: any
    config?: any
    collapsible?: 'icon' | 'full'
}

const props = withDefaults(defineProps<SidebarProps>(), {
    collapsible: 'icon',
})


const user = computed(() => {
    return {
        id: props.user.id as number,
        first_name: props.user.first_name as string,
        last_name: props.user.last_name as string,
        full_name: props.user.full_name as string,
        email: props.user.email as string,
        avatar: props.user.avatar as string,
        phone: props.user.phone as string,
        role: props.user.role as 'admin' | 'user',
    }
});

type navItems = {
    title: string
    url: string
}

type NavMainType = {
    title: string;
    url: string;
    icon?: LucideIcon;
    items?: navItems[];
}


const data: { navMain: NavMainType[] } = {
    navMain: [
        {
            title: "Dashboard",
            url: "/admin/dashboard",
            icon: LayoutDashboard,
        },
        {
            title: "Organizations",
            url: "/admin/organizations",
            icon: Building2,
        },
        {
            title: "Users",
            url: "/admin/users",
            icon: Users,
        },
        // {
        //     title: "Billing",
        //     url: "/admin/payment-logs",
        //     icon: ReceiptText,
        // },
        {
            title: "Invoices & Payments",
            url: "/admin/payment-logs",
            icon: CreditCard,
        },
        {
            title: "Support desk",
            url: "/admin/support",
            icon: Handshake,
        },
        {
            title: "Team",
            url: "/admin/team/users",
            icon: UserCog,
        },

        // ------------------ Divider Section ------------------

        {
            title: "Roles",
            url: "/admin/team/roles",
            icon: ShieldCheck,
        },
        {
            title: "Subscription plans",
            url: "/admin/plans",
            icon: Layers,
        },
        {
            title: "Lifetime Addons",
            url: "/admin/lifetime-addons",
            icon: PackagePlus,
        },
        {
            title: "FAQs",
            url: "/admin/faqs",
            icon: FileQuestion,
        },
        {
            title: "Reviews",
            url: "/admin/testimonials",
            icon: Star,
        },
        {
            title: "Settings",
            url: "/admin/settings/general",
            icon: Settings2,
        },
    ],
};



</script>



<template>
    <Sidebar>
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="bg-black/5 hover:bg-black/10">
                        <Link href="/admin/dashboard"
                            class="flex items-center justify-start rounded-lg pl-4 cursor-pointer">
                            <img :src="NyifeLogo" alt="Nyife Logo" class="h-full min-h-10" />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>
        <SidebarContent>
            <NavMain :items="data.navMain" />
        </SidebarContent>
        <SidebarFooter>
            <NavUser :user="user" />
        </SidebarFooter>
        <SidebarRail />
    </Sidebar>
</template>
