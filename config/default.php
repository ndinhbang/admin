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
        ],
        'permissions' => [
            'manage.abilities' => [
                'name'        => 'manage.abilities',
                'title'       => 'Quản lý quyền hạn',
                'roles'       => ['admin', 'superadmin'],
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
            'boss'       => [
                'name'  => 'boss__%s',
                'title' => 'Chủ cửa hàng',
                'level' => 50,
            ],
            'manager'    => [
                'name'  => 'manager__%s',
                'title' => 'Quản lý',
                'level' => 40,
            ],
            'cashier'    => [
                'name'  => 'cashier__%s',
                'title' => 'Nhân viên Thu ngân',
                'level' => 30,
            ],
            'waiter'     => [
                'name'  => 'waiter__%s',
                'title' => 'Nhân viên Chạy bàn',
                'level' => 20,
            ],
            'chef'       => [
                'name'  => 'chef__%s',
                'title' => 'Nhân viên Bếp',
                'level' => 10,
            ],
            'shipper'    => [
                'name'  => 'shipper__%s',
                'title' => 'Nhân viên Giao hàng',
                'level' => 5,
            ],
        ],
        'permissions' => [
            // Quản lý
            'manage.places'   => [
                'name'        => 'manage.places',
                'title'       => 'Quản lý của hàng',
                'roles'       => ['admin', 'superadmin', 'boss'],
            ],
            'manage.overview'  => [
                'name'        => 'manage.overview',
                'title'       => 'Xem tổng quan',
                'roles'       => ['admin', 'superadmin', 'boss'],
            ],
            'manage.orders'  => [
                'name'        => 'manage.orders',
                'title'       => 'Quản lý đơn hàng',
                'roles'       => ['admin', 'superadmin', 'boss'],
            ],
            'manage.products'  => [
                'name'        => 'manage.products',
                'title'       => 'Quản lý sản phẩm',
                'roles'       => ['admin', 'superadmin', 'boss'],
            ],
            'manage.categories'  => [
                'name'        => 'manage.categories',
                'title'       => 'Quản lý danh mục',
                'roles'       => ['admin', 'superadmin', 'boss'],
            ],
            'manage.inventory'  => [
                'name'        => 'manage.inventory',
                'title'       => 'Quản lý kho',
                'roles'       => ['admin', 'superadmin', 'boss'],
            ],
            'manage.promotions'  => [
                'name'        => 'manage.promotions',
                'title'       => 'Quản lý khuyến mãi',
                'roles'       => ['admin', 'superadmin', 'boss'],
            ],

            // CRM

            'crm.customers'   => [
                'name'        => 'crm.customers',
                'title'       => 'Quản lý khách hàng',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'crm.suppliers'   => [
                'name'        => 'crm.suppliers',
                'title'       => 'Quản lý nhà cung cấp',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'crm.shippers'   => [
                'name'        => 'crm.shippers',
                'title'       => 'Quản lý đơn vị vận chuyển',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],


            // Báo cáo

            'reports.revenues'   => [
                'name'        => 'reports.revenues',
                'title'       => 'Báo cáo doanh số',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'reports.profits'   => [
                'name'        => 'reports.profits',
                'title'       => 'Báo cáo doanh số',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'reports.net-profits'   => [
                'name'        => 'reports.net-profits',
                'title'       => 'Báo cáo lãi lỗ',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],

            // Sổ quỹ
            'cashflow.overview'   => [
                'name'        => 'cashflow.overview',
                'title'       => 'Sổ quỹ tổng quan',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'cashflow.ledger'   => [
                'name'        => 'cashflow.ledger',
                'title'       => 'Sổ quỹ thu chi',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'cashflow.approve-expense'   => [
                'name'        => 'cashflow.approve-expense',
                'title'       => 'Duyệt phiếu chi',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],

            // Thiết lập
            'settings.general'   => [
                'name'        => 'settings.general',
                'title'       => 'Thiết lập chung',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'settings.employees'   => [
                'name'        => 'settings.employees',
                'title'       => 'Thiết lập nhân viên',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'settings.employees'   => [
                'name'        => 'settings.employees',
                'title'       => 'Thiết lập bán hàng',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'settings.printers'   => [
                'name'        => 'settings.printers',
                'title'       => 'Thiết lập máy in',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'settings.payments'   => [
                'name'        => 'settings.payments',
                'title'       => 'Thiết lập thanh toán',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ],
            'settings.order_states'   => [
                'name'        => 'settings.order_states',
                'title'       => 'Thiết lập trạng thái đơn hàng',
                'roles'       => ['admin', 'superadmin', 'boss', 'manager__%s'],
            ]
        ]
    ],
];
