<?php

return [

    /*
     * 站点标题
     */
    'name' => '驾校管理后台',

    /*
     * 页面顶部 Logo
     */
    'logo' => '<b>驾校</b> 管理后台',

    /*
     * 页面顶部小 Logo
     */
    'logo-mini' => '<b>驾校</b>',

    /*
     * 路由配置
     */
    'route' => [
        // 路由前缀
        'prefix' => 'admin',
        // 控制器命名空间前缀
        'namespace' => 'App\\Admin\\Controllers',
        // 默认中间件列表
        'middleware' => ['web', 'admin'],
    ],

    /*
     * Laravel-Admin 的安装目录
     */
    'directory' => app_path('Admin'),

    /*
     * Laravel-Admin 页面标题
     */
    'title' => '驾校 管理后台',

    /*
     * 是否使用 https
     */
    'secure' => env('USE_HTTPS', false),

    /*
     * Laravel-Admin 用户认证设置
     */
    'auth' => [
        'guards' => [
            'admin' => [
                'driver' => 'session',
                'provider' => 'admin',
            ],
        ],

        'providers' => [
            'admin' => [
                'driver' => 'eloquent',
                'model' => Encore\Admin\Auth\Database\Administrator::class,
            ],
        ],
    ],

    /*
     * Laravel-Admin 文件上传设置
     */
    'upload' => [
        // 对应 filesystem.php 中的 disks
        'disk' => 'public',

        'directory' => [
            'image' => 'images',
            'file' => 'files',
        ],
    ],

    /*
     * Laravel-Admin 数据库设置
     */
    'database' => [

        // 数据库连接名称，留空即可
        'connection' => '',

        // 管理员用户表及模型
        'users_table' => 'admin_users',
        'users_model' => Encore\Admin\Auth\Database\Administrator::class,

        // 角色表及模型
        'roles_table' => 'admin_roles',
        'roles_model' => Encore\Admin\Auth\Database\Role::class,

        // 权限表及模型
        'permissions_table' => 'admin_permissions',
        'permissions_model' => Encore\Admin\Auth\Database\Permission::class,

        // 菜单表及模型
        'menu_table' => 'admin_menu',
        'menu_model' => Encore\Admin\Auth\Database\Menu::class,

        // 多对多关联中间表
        'operation_log_table' => 'admin_operation_log',
        'user_permissions_table' => 'admin_user_permissions',
        'role_users_table' => 'admin_role_users',
        'role_permissions_table' => 'admin_role_permissions',
        'role_menu_table' => 'admin_role_menu',
    ],

    /*
     * Laravel-Admin 操作日志设置
     */
    'operation_log' => [

        'enable' => true,

        /*
         * 不记操作日志的路由
         */
        'except' => [
            'admin/auth/logs*',
        ],
    ],

    /*
     * 页面风格
     * @see https://adminlte.io/docs/2.4/layout
     */
    'skin' => 'skin-green-light',

    /*
    |---------------------------------------------------------|
    |LAYOUT OPTIONS | fixed                                   |
    |               | layout-boxed                            |
    |               | layout-top-nav                          |
    |               | sidebar-collapse                        |
    |               | sidebar-mini                            |
    |---------------------------------------------------------|
     */
    'layout' => ['sidebar-mini', 'sidebar-collapse'],
    /*
    |--------------------------------------------------------------------------
    | Login page background image
    |--------------------------------------------------------------------------
    |
    | 登录页面的背景图设置
    |
    */
    'login_background_image' => '',
    /*
    |--------------------------------------------------------------------------
    | Show version at footer
    |--------------------------------------------------------------------------
    |
    | 是否在页面的右下角显示当前laravel-admin的版本
    |
    */
    'show_version' => false,
    /*
    |--------------------------------------------------------------------------
    | Show environment at footer
    |--------------------------------------------------------------------------
    |
    | 是否在页面的右下角显示当前的环境
    |
    */
    'show_environment' => false,
    /*
    |--------------------------------------------------------------------------
    | Enable default breadcrumb
    |--------------------------------------------------------------------------
    |
    | 是否开启页面的面包屑导航
    */
    'enable_default_breadcrumb' => true,

    /*
    |--------------------------------------------------------------------------
    | Extension Directory
    |--------------------------------------------------------------------------
    |
    | 如果你要运行`php artisan admin:extend`命令来开发扩展，需要配置这一项，来存放你的扩展文件
    */
    'extension_dir' => app_path('Admin/Extensions'),

    /*
     * 扩展设置.
     */
    'extensions' => [

        'chartjs' => [

            // Set to `false` if you want to disable this extension
            'enable' => true,
        ]
    ]
];
