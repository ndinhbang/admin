<?php

return [
    'roles'       => [
        'superadmin' => [
            'name'  => 'superadmin',
            'title' => 'superadmin',
            'level' => 100,
        ],
        'admin'      => [
            'name'  => 'admin',
            'title' => 'Quản trị hệ thống',
            'level' => 80,
        ],
        'boss'       => [
            'name'  => 'boss',
            'title' => 'Chủ cửa hàng',
            'level' => 50,
        ],
        'manager'    => [
            'name'  => 'manager',
            'title' => 'Quản lý',
            'level' => 40,
        ],
        'cashier'    => [
            'name'  => 'cashier',
            'title' => 'Thu ngân',
            'level' => 30,
        ],
        'waiter'     => [
            'name'  => 'waiter',
            'title' => 'Bồi bàn',
            'level' => 20,
        ],
        'chef'       => [
            'name'  => 'chef',
            'title' => 'Nv Bếp',
            'level' => 10,
        ],
    ],
    'permissions' => [
        // default permission for a tenant
        'tenants' => [
            'tenant--view.dashboard'  => [
                'name'  => 'tenant--view.dashboard',
                'title' => 'Xem tổng quan',
                'roles' => ['admin', 'superuser', 'boss'],
            ],
            'tenant--manage.settings' => [
                'name'  => 'tenant--manage.settings',
                'title' => 'Thiết lập',
                'roles' => ['admin', 'superuser', 'boss'],
            ],
            'tenant--manage.roles'    => [
                'name'  => 'tenant--manage.roles',
                'title' => 'Phân quyền nhân viên',
                'roles' => ['admin', 'superuser', 'boss'],
            ],
            'tenant--manage.users'    => [
                'name'  => 'tenant--manage.users',
                'title' => 'Quản lý nhân sự',
                'roles' => ['admin', 'superuser', 'boss'],
            ],
            'tenant--view.reports'    => [
                'name'  => 'tenant--view.reports',
                'title' => 'Xem báo cáo',
                'roles' => ['admin', 'superuser', 'boss'],
            ],
            'tenant--view.categories' => [
                'name'  => 'tenant--view.categories',
                'title' => 'Xem danh mục hàng bán',
                'roles' => ['admin', 'superuser', 'boss', 'manager', 'cahsier', 'waiter', 'chef'],
            ],
        ],
        // default permissions for all
        'root'    => [
            'manage.tenants'   => [
                'name'  => 'manage.tenants',
                'title' => 'Quản lý cửa hàng',
                'roles' => ['admin', 'superuser', 'boss'],
            ],
            'manage.users'     => [
                'name'  => 'manage.users',
                'title' => 'Quảng lý tài khoản',
                'roles' => ['admin', 'superuser'],
            ],
            'manage.roles'     => [
                'name'  => 'manage.roles',
                'title' => 'Phân quyền',
                'roles' => ['admin', 'superuser'],
            ],
            'manage.abilities' => [
                'name'  => 'manage.abilities',
                'title' => 'Quản lý quyền hạn',
                'roles' => ['admin', 'superuser'],
            ],
        ],
    ],
];
