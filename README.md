# kicoephp

## 关于
超级小巧简单的MVC php框架
直接把psr-4自动加载官方例子照搬了。。。反正用来学习的

配置：
* php >= 5.3
* PDO

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
    -index.php  入口文件
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

首先是controller里是控制器,类名和文件名一致，必要继承核心类中的Controller
```php
<?php
namespace app\controller;

use \kicoe\Core\Controller;

class Index extends Controller
{
	public function index()
	{
		$this->show();
	}
}
```
控制器命名空间都为 `app\controller` 
核心库命名空间为 `\kicoe\Core\...`
Controller有一般MVC都有的*show()*函数自动加载位于 **/app/view/控制器/操作名.php** 的视图,或者也可以在show()中指定
```php
$this->show(love/live);     //加载view中love/live.php视图文件
```
也可以通过
```php
$this->assign('a','123');   //这样就可以在页面中使用$a变量了
//同样支持直接传入键值对数组,因为本来就是用extract()解析的--|
$this->assign(['a'=>123,'b'=>234]);
```
---
####查询构造（要优雅
```php
<?php
namespace app\controller;

use \kicoe\Core\Controller;
use \kicoe\Core\Query;  //引入数据库操作类

class Index extends Controller
{
	public function index()
	{
	    //这里返回的是'username'为kicoe的字段数据
		Query::table('user')->where('username','kicoe')->select();
		//这里向user表插入数据
		Query::table('user')->insert([['username'=>'ll','password'=>'***'],]);
	}
}
```
和优雅的laravel好像。。。
 

 



