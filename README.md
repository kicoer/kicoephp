# kicoephp

## 关于
超级小巧简单的phpMVC框架，用来搭博客的

配置：

* php >= 5.3
* PDO

## 结构
```
-app/
	-controller/    控制器
    -model/         模型
    -view/          视图
    -config.php     配置文件
-core/
	-library/       框架核心库
    -AutoLoad.php
    -load.php       框架加载文件
-public/
	-static/
    -index.php      入口文件
```
和普通MVC没什么太大区别
## 使用
#### nginx
把页面链接指向`index.php?k=`，nginx配置如下：
```
location / {
	...//原来的代码
    if (!-e $request_filename) {
        rewrite ^(.*)$ /index.php?k=$1 last;
        break;
    }
}
```
再将网站的根目录设置为public文件夹路径
#### 配置文件
位于app目录下的 `config.php` 填上自己的数据库配置吧
```php
return [
	//数据库配置
	'db' => [
		// 服务器地址
	    'hostname'    => 'localhost',
	    // 数据库名
	    'database'    => '',
	    // 数据库用户名
	    'username'    => '',
	    // 数据库密码
	    'password'    => ''
	]
];
```
#### M  （模型）
首先来看看查询构造器
```php
<?php
namespace app\controller;

use \kicoe\Core\Controller;
use \kicoe\Core\Query;  //引入数据库操作类

class Index extends Controller
{
	public function index()
	{
		//这里向user表插入数据
		Query::table('user')->insert(['username'=>'ll','password'=>'***']);
		//这里是向user表删除数据
		Query::table('user')->where('username','=','kicoe')->delete();
	    //这里查找user表中'username'为kicoe的字段数据中id的值
		Query::table('user')->where('username','kicoe')->select('id');
		//这里修改user表中所有id小于10的statu字段为0,orwhere()也可以的哦
		Query::table('user')->where('id','<','10')->update(['statu'=>0]);
	}
}
```
和优雅的laravel好像。。。
 

 



