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
        // 这里向user表插入数据
        Query::table('user')->insert(['username'=>'ll','password'=>'***']);
        // 这里是向user表删除数据
        Query::table('user')->where('username','=','kicoe')->delete();
        // 这里查找user表中'username'为kicoe的字段数据中id的值
        Query::table('user')->where('username','kicoe')->select('id');
        // 这里修改user表中所有id小于10的statu字段为0,orwhere()也可以的哦
        Query::table('user')->where('id','<','10')->update(['statu'=>0]);
    }
}
```
和优雅的laravel好像。。。
首先定义模型于 `app/model/User.php`
```php
<?php
namespace app\model;

use \kicoe\Core\Model;

/**
* user表的模型类
*/
class User extends Model
{
    public function __construct()
    {
        // 可以自定义表名，或默认为类名小写
        $this->table = "ex_user";
    }
}
```
关于模型的用法
```php
<?php
namespace app\controller;

use \kicoe\Core\Controller;
use \app\model\User;
class Index extends Controller
{
    public function index()
    {
        // 获取ORM对象
        $user = new User;
        // 获取id为2的数据，注意get需要用来获取主键
        $user->get(['id'=>2]);
        // 可以查询到获取该列的其他数据
        echo $user->name;
        // 也可以直接给对象属性赋值后执行insert()插入数据
        $user->name = 'kicoe';
        $user->password = sha1('pa');
        $user->insert();
        // 当然insert传入参数后可以构造查询
        $user->insert(['name','passwd'], ['kicoe',sha1('pa')], ['poi',sha1('pom')]);
        // 使用set构造where
        $user->set(['id','<',10], 'or', ['name','kicoe'], ['password',sha1('pa')]);
        // update更新数据
        $user->update(['name'=>'k']);
        // select查询数据,默认查询所有
        $user->select('name');
        // delete删除数据，未使用set / get构造查询条件则会删除所有
        $user->delete();
    }
}
```
模型与查询构造器都可以使用
```php
// 构造order by与limit
order('列名','desc / asc');
limit($i,$n);
// 自定义查询
query('select * from user where id = ?',[$id]);
// 自定义执行
execute('select * from user where id = ?',[$id]);
```
#### V  （视图）
在 `app/view` 中定义 `Controller/action.php`
写php就可以了
#### C  （控制器）
以上模型的例子里就用到了控制器，定义在`app/controller`中，控制器名大写且与类名一致
显示视图时可以使用
```php
<?php
namespace app\controller;

use \kicoe\Core\Controller;

class Index extends Controller
{
    public function index()
    {
        // 默认显示视图app/view/Index/index.php
        $this->show();
        // 当然也可以自定义，位于app/view/a/b.php
        $this->show('a/b');
    }
}
```
#### 关于Session和提交数据post get的操作
```php
<?php
namespace app\controller;

use \kicoe\Core\Controller;
use \kicoe\Core\Request;
use \kicoe\Core\Session;

class Index extends Controller
{
    public function index()
    {
        $request = Request::getInstance();
        // 获取用户post提交的name数据
        $name = $request->post('name');
        // session操作
        Session::set('name','kicoe');
        if (Session::has('name')) {
            echo Session::get('name');
        }
    }
}
```
## 完结撒花
花了一些时间，终于把这个小小的框架写好了，自己用用就好

嗯，就是这样



