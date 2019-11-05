<?php

return [
    'root' => [
        'roles' => [
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
        ],
        'permissions' => [
            'manage.abilities' => [
                'name'        => 'manage.abilities',
                'title'       => 'Quản lý quyền hạn',
                'roles'       => array('admin', 'superadmin'),
            ],
            'manage.roles'     => [
                'name'        => 'manage.roles',
                'title'       => 'Quản lý chức vụ',
                'roles'       => ['admin', 'superadmin'],
            ],
            'manage.users'     => [
                'name'        => 'manage.users',
                'title'       => 'Quảng lý tài khoản',
                'roles'       => ['admin', 'superadmin', 'boss'],
            ],
            'own.places'       => [
                'name'        => 'own.places',
                'title'       => 'Quyền sở hữu của hàng',
                'roles'       => ['admin', 'superadmin', 'boss'],
            ],
        ]
    ],
    'place' => [
        'roles' => [
            'manager'    => [
                'name'  => 'manager__%s',
                'title' => 'Quản lý',
                'level' => 40,
            ],
            'cashier'    => [
                'name'  => 'cashier__%s',
                'title' => 'Thu ngân',
                'level' => 30,
            ],
            'waiter'     => [
                'name'  => 'waiter__%s',
                'title' => 'Bồi bàn',
                'level' => 20,
            ],
            'chef'       => [
                'name'  => 'chef__%s',
                'title' => 'Nv Bếp',
                'level' => 10,
            ],
            'shipper'    => [
                'name'  => 'shipper__%s',
                'title' => 'Nv giao hàng',
                'level' => 5,
            ],
        ],
        'permissions' => [
            'manage.places'   => [
                'name'        => 'manage.places__%s',
                'title'       => 'Quản lý của hàng',
                'roles'       => ['admin', 'superadmin', 'boss'],
            ],
            'manage.staffs'   => [
                'name'        => 'manage.staffs__%s',
                'title'       => 'Quản lý nhân viên',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'view.dashboard'  => [
                'name'        => 'view.dashboard__%s',
                'title'       => 'Xem tổng quan',
                'roles'       => ['admin', 'superadmin', 'boss'],
            ],
            'manage.settings' => [
                'name'        => 'manage.settings__%s',
                'title'       => 'Thiết lập',
                'roles'       => ['admin', 'superadmin', 'boss'],
            ],
            'view.reports'    => [
                'name'        => 'view.reports__%s',
                'title'       => 'Xem báo cáo',
                'roles'       => ['admin', 'superadmin', 'boss'],
            ],
            'manage.printers'    => [
                'name'        => 'manage.printers__%s',
                'title'       => 'Cài đặt máy in',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'manage.orders'    => [
                'name'        => 'manage.orders__%s',
                'title'       => 'Quản lý đơn hàng',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s', 'cashier__%s'],
            ],
            'manage.customers'    => [
                'name'        => 'manage.customers__%s',
                'title'       => 'Quản lý khách hàng',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s', 'cashier__%s'],
            ],
            'manage.suppliers'    => [
                'name'        => 'manage.suppliers__%s',
                'title'       => 'Quản lý nhà cung cấp',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s', 'cashier__%s'],
            ],
            'manage.supplies'    => [
                'name'        => 'manage.supplies__%s',
                'title'       => 'Quản lý nhà cung cấp',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s', 'cashier__%s'],
            ],
            'manage.invoices'    => [
                'name'        => 'manage.invoices__%s',
                'title'       => 'Quản lý thu chi',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'manage.order_states'    => [
                'name'        => 'manage.order_states__%s',
                'title'       => 'Quản lý trạng thái đơn hàng',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s', 'cashier__%s'],
            ],
        ]
    ],
];
