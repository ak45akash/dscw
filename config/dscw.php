<?php

return [

    'business' => [
        'name' => 'Diamond Steam Car Wash',
        'tagline' => 'Premium car care with steam-powered precision',
    ],

    'featured_services' => [
        'max' => 5,
        'min' => 3,
    ],

    'permissions' => [
        'dashboard.view',
        'bookings.view',
        'bookings.manage',
        'services.view',
        'services.manage',
        'content.view',
        'content.manage',
        'marketing.view',
        'marketing.manage',
        'business.view',
        'business.manage',
        'reports.view',
        'system.view',
        'system.manage',
    ],

    'roles' => [
        'super_admin' => [
            'label' => 'Super Admin',
            'permissions' => ['*'],
        ],
        'admin' => [
            'label' => 'Admin / Manager',
            'permissions' => [
                'dashboard.view',
                'bookings.view',
                'bookings.manage',
                'services.view',
                'services.manage',
                'content.view',
                'content.manage',
                'marketing.view',
                'marketing.manage',
                'business.view',
                'business.manage',
                'reports.view',
            ],
        ],
    ],

    'settings_groups' => [
        'business',
        'theme',
        'booking',
        'seo',
        'payment',
        'email',
        'analytics',
    ],

];
