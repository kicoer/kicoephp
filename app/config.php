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
    // 路由配置,设置为[]则自动路由
    'route' => [
        'index' => 'index@index',
        'art/id' => 'index@article'
    ],
    // 缓存或日志文件目录,APP_PATH.'cc'确保可写
    'cc' => 'cc',
    // false关闭测试，将不会开启路由缓存和报错
    'test' => true
];
