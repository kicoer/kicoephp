<?php

return [
    //数据库配置
    'db' => [
        // 服务器地址
        'hostname'    => 'localhost',
        // 数据库名
        'database'    => 'blog',
        // 数据库用户名
        'username'    => 'kicoe',
        // 数据库密码
        'password'    => 'kicoephp'
    ],
    // 路由配置
    'route' => [
        'i/i' => 'index/index',
        'article/id' => 'index/article',
    ]
];
