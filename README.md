# kicoephp

## 关于
超级小巧简单的MVC php框架
直接把psr-4自动加载官方例子照搬了。。。反正用来学习的

配置：
* php >= 5.3

## 结构

```
-app/
	-controller/
    -model/
    -view/
    -config.php
-core/
	-library/
    -AutoLoad.php
    -load.php
-public/
	-static/
    -index.php
```
和普通MVC没什么太大区别

## 路由

***把页面链接指向`index.php?k=` nginx配置如下：***
首先是开启pathinfo，检查php.ini中cgi.fix_pathinfo=0;
```
location ~ \.php {
      fastcgi_split_path_info ^(.+\.php)(.*)$;
      fastcgi_param PATH_INFO $fastcgi_path_info;
      ...
}

```
然后是路由跳转

```
location / {
	...//原来的代码
    if (!-e $request_filename) {
        rewrite ^(.*)$ /index.php?k=$1 last;
        break;
    }
}
```

## 使用

。。。自己用的东西以后慢慢写吧



