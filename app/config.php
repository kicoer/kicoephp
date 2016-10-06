<?php

return [
    //数据库配置
    'db' => [
        // 服务器地址
        'hostname'    => 'localhost',
        // 数据库名
        'database'    => 'blog',
        // 数据库用户名
        'username'    => 'blog',
        // 数据库密码
        'password'    => '***'
    ],
    // 路由配置
    'route' => [
        'article/page' => 'index/index',
        'article/id' => 'index/article',
        'link/index' => 'index/link'
    ]
];
