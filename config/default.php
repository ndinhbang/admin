<?php

return [
    'roles' => [
        'root' => [
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
        'place' => [
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
                'title' => 'Nhân viên Kho/Bếp',
                'level' => 10,
            ],
            'shipper'    => [
                'name'  => 'shipper__%s',
                'title' => 'Nhân viên Giao hàng',
                'level' => 5,
            ],
        ],
    ],
    'permissions' => [
        'admin.abilities' => [
            'name'        => 'admin.abilities',
            'title'       => 'Quản lý quyền hạn',
            'roles'       => ['superadmin'],
        ],
        'admin.roles'     => [
            'name'        => 'admin.roles',
            'title'       => 'Quản lý chức vụ',
            'roles'       => ['superadmin'],
        ],
        'admin.users'     => [
            'name'        => 'admin.users',
            'title'       => 'Quản lý tài khoản',
            'roles'       => ['admin', 'superadmin'],
        ],
        'admin.places'       => [
            'name'        => 'admin.places',
            'title'       => 'Quản lý của hàng',
            'roles'       => ['admin', 'superadmin'],
        ],
        
        // Quản lý cửa hàng - gia hạn, hợp đồng, tạo thêm/chỉnh sửa cửa hàng...
        'manage.places'   => [
            'name'        => 'manage.places',
            'title'       => 'Quản lý của hàng',
            'roles'       => ['admin', 'superadmin', 'boss__%s'],
        ],
        // Quản lý
        'pos'  => [
            'name'        => 'pos',
            'title'       => 'Bán hàng',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s', 'cashier__%s', 'chef__%s', 'waiter__%s'],
        ],
        'manage.overview'  => [
            'name'        => 'manage.overview',
            'title'       => 'Xem tổng quan',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s', 'cashier__%s', 'chef__%s'],
        ],
        'manage.orders'  => [
            'name'        => 'manage.orders',
            'title'       => 'Quản lý đơn hàng',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s', 'cashier__%s', 'chef__%s', 'waiter__%s'],
        ],
        'manage.order-items'  => [
            'name'        => 'manage.order-items',
            'title'       => 'Quản lý hàng trong đơn',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s', 'cashier__%s', 'chef__%s', 'waiter__%s'],
        ],
        'manage.products'  => [
            'name'        => 'manage.products',
            'title'       => 'Quản lý sản phẩm',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s', 'chef__%s'],
        ],
        'manage.categories'  => [
            'name'        => 'manage.categories',
            'title'       => 'Quản lý danh mục',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s'],
        ],
        'manage.inventory'  => [
            'name'        => 'manage.inventory',
            'title'       => 'Quản lý kho',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s', 'chef__%s'],
        ],
        'manage.purchases'  => [
            'name'        => 'manage.purchases',
            'title'       => 'Quản lý nhập hàng',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s', 'cashier__%s', 'chef__%s'],
        ],
        'manage.promotions'  => [
            'name'        => 'manage.promotions',
            'title'       => 'Quản lý khuyến mãi',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s'],
        ],

        // CRM
        'crm.customers'   => [
            'name'        => 'crm.customers',
            'title'       => 'Quản lý khách hàng',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s', 'cashier__%s'],
        ],
        'crm.suppliers'   => [
            'name'        => 'crm.suppliers',
            'title'       => 'Quản lý nhà cung cấp',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s', 'cashier__%s'],
        ],
        'crm.shippers'   => [
            'name'        => 'crm.shippers',
            'title'       => 'Quản lý đơn vị vận chuyển',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s', 'cashier__%s'],
        ],


        // Báo cáo
        'reports.revenues'   => [
            'name'        => 'reports.revenues',
            'title'       => 'Báo cáo doanh số',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s', 'cashier__%s'],
        ],
        'reports.profits'   => [
            'name'        => 'reports.profits',
            'title'       => 'Báo cáo lợi nhuận',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s'],
        ],
        'reports.net-profits'   => [
            'name'        => 'reports.net-profits',
            'title'       => 'Báo cáo lãi lỗ',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s'],
        ],
        'reports.stocks'   => [
            'name'        => 'reports.stocks',
            'title'       => 'Báo cáo tồn kho',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s', 'chef__%s'],
        ],

        // Sổ quỹ
        'cashflow.overview'   => [
            'name'        => 'cashflow.overview',
            'title'       => 'Sổ quỹ tổng quan',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s'],
        ],
        'cashflow.ledger'   => [
            'name'        => 'cashflow.ledger',
            'title'       => 'Sổ quỹ thu chi',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s', 'cashier__%s', 'chef__%s'],
        ],
        'cashflow.approve-expense'   => [
            'name'        => 'cashflow.approve-expense',
            'title'       => 'Duyệt phiếu chi',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s'],
        ],

        // Thiết lập
        'settings.general'   => [
            'name'        => 'settings.general',
            'title'       => 'Thiết lập chung',
            'roles'       => ['admin', 'superadmin', 'boss__%s'],
        ],
        'settings.employees'   => [
            'name'        => 'settings.employees',
            'title'       => 'Thiết lập nhân viên',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s'],
        ],
        'settings.sales'   => [
            'name'        => 'settings.sales',
            'title'       => 'Thiết lập bán hàng',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s'],
        ],
        'settings.print-form'   => [
            'name'        => 'settings.print-form',
            'title'       => 'Thiết lập mẫu in',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s'],
        ],
        'settings.order_states'   => [
            'name'        => 'settings.order_states',
            'title'       => 'Thiết lập trạng thái đơn hàng',
            'roles'       => ['admin', 'superadmin', 'boss__%s', 'manager__%s'],
        ]
    ],
    'categories' => [
        'unit' => ['Cái', 'Chiếc', 'Phần', 'Chai', 'Điếu', 'Kg', 'Gram', 'Lít', 'Gói', 'Bao', 'Quả', 'Lon', 'Đồng', 'Túi', 'Thùng', 'Hộp', 'Lốc', 'Mét', 'Milimet'],
        'expense' => [
            'Chi mua hàng',
            'Chi đầu kì',
            'Chi tạm ứng',
            'Chi hoàn ứng',
            'Chi rút vốn'
        ],
        'revenue' => [
            'Thu bán hàng',
            'Thu xuất trả',
            'Thu góp vốn',
            'Thu tạm ứng',
            'Thu hoàn ứng'
        ]
    ]
];
