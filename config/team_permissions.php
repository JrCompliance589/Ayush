<?php

/**
 * Team Member Permissions Configuration
 * 
 * This file defines all available permissions that can be assigned to team members.
 * Owner has all permissions by default.
 * Manager and Agent roles can be granted specific permissions.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Available Permissions
    |--------------------------------------------------------------------------
    |
    | Each permission has:
    | - key: unique identifier used in database and code
    | - label: display name for UI
    | - description: explanation of what this permission allows
    | - routes: array of route prefixes this permission controls access to
    | - icon: icon name for UI display (optional)
    |
    */
    'permissions' => [
        [
            'key' => 'dashboard',
            'label' => 'Dashboard',
            'description' => 'Access to view dashboard and analytics',
            'routes' => ['dashboard'],
            'icon' => 'layout-dashboard',
            'default' => true, // Enabled by default for new team members
        ],
        [
            'key' => 'chats',
            'label' => 'Chats',
            'description' => 'Access to view and manage chats/conversations',
            'routes' => ['chats', 'chat', 'tickets', 'notes'],
            'icon' => 'messages-square',
            'default' => true,
        ],
        [
            'key' => 'contacts',
            'label' => 'Contacts',
            'description' => 'Access to view and manage contacts',
            'routes' => ['contacts', 'contact-groups'],
            'icon' => 'book-user',
            'default' => false,
        ],
        [
            'key' => 'campaigns',
            'label' => 'Campaigns',
            'description' => 'Access to view and manage campaigns',
            'routes' => ['campaigns'],
            'icon' => 'megaphone',
            'default' => false,
        ],
        [
            'key' => 'templates',
            'label' => 'Message Templates',
            'description' => 'Access to view and manage message templates',
            'routes' => ['templates'],
            'icon' => 'folder-tree',
            'default' => false,
        ],
        [
            'key' => 'automation',
            'label' => 'Automation',
            'description' => 'Access to automation and chatbot features',
            'routes' => ['automation'],
            'icon' => 'bot',
            'default' => false,
        ],
        [
            'key' => 'team',
            'label' => 'Team',
            'description' => 'Access to view team members (manage requires owner role)',
            'routes' => ['team'],
            'icon' => 'users',
            'default' => false,
        ],
        [
            'key' => 'settings',
            'label' => 'Settings',
            'description' => 'Access to organization settings',
            'routes' => ['settings'],
            'icon' => 'settings',
            'default' => false,
        ],
        [
            'key' => 'billing',
            'label' => 'Billing & Subscription',
            'description' => 'Access to billing and subscription management',
            'routes' => ['billing', 'subscription'],
            'icon' => 'credit-card',
            'default' => false,
        ],
        [
            'key' => 'support',
            'label' => 'Support',
            'description' => 'Access to support tickets',
            'routes' => ['support'],
            'icon' => 'handshake',
            'default' => true,
        ],
        [
            'key' => 'developer_tools',
            'label' => 'Developer Tools',
            'description' => 'Access to API tokens and developer tools',
            'routes' => ['developer-tools'],
            'icon' => 'code-xml',
            'default' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Permissions by Role
    |--------------------------------------------------------------------------
    |
    | Define which permissions are granted by default when a new team member
    | is added with a specific role.
    |
    */
    'defaults' => [
        'owner' => '*', // All permissions
        'manager' => ['dashboard', 'chats', 'contacts', 'campaigns', 'templates', 'support'],
        'agent' => ['dashboard', 'chats', 'support'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route to Permission Mapping
    |--------------------------------------------------------------------------
    |
    | This is auto-generated from the permissions array above.
    | Used by the middleware to check access.
    |
    */
];
