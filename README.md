# kicoephp

## 关于

超级小巧简单的phpMVC框架，用来搭博客的

依赖：

* php >= 5.4
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
    -load.php       框架加载文件
-public/
    -static/
    -index.php      入口文件
```
和普通MVC没什么太大区别

## 使用

#### nginx
页面链接重定向`index.php?k=`，nginx配置如下：
```
location / {
    try_files $uri $uri/ /index.php?k=$uri;
}
```
再将网站的根目录设置为public文件夹路径
#### 配置文件
位于app目录下的 `config.php` 填上自己的数据库与路由配置吧
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
    ],
    // 路由配置
    'route' => [
        // 将i/i操作指向index/index操作
        'i/i' => 'index/index',
        // 将link指向index/link
        'link/index' => 'index/link'
    ]
];
```
#### M  （模型）
首先来看看查询构造器
```php
<?php
namespace app\controller;

use \kicoe\Core\Controller;
use \kicoe\Core\Query;  //引入数据库查询类

class Index extends Controller
{
    public function index()
    {
        // 向user表插入数据
        Query::table('user')->insert(['username'=>'ll', 'password'=>'***']);
        Query::table('user')->insert(['username', 'password'], [ ['11','***'], [12,'..'] ]);
        // 向user表删除数据
        Query::table('user')->where('username', '=', 'kicoe')->delete();
        // 查找user表中`username`='kicoe'的字段数据中id的值
        Query::table('user')->where('username','kicoe')->select('id');
        // 查找user表中`username`为'kicoe'或'admin'的字段数据中所有值，结果为id=>*的关联数组
        Query::table('user')->where('username', 'in', ['kicoe', 'admin'])->select('*', 'id');
        // 修改user表中所有`id`小于10的statu字段为0
        Query::table('user')->where('id','<',10)->update(['statu'=>0]);
      	// 关于orwhere的正确用法 `username`='admin' or `username`='kicoe'
        Query::table('user')->where('username','admin')->orwhere('username','kicoe')->select('*');
    }
}
```
定义模型于 `app/model/User.php`
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
        // 获取id为2的数据，注意get需要用来获取主键，第二个参数主键默认为'id'
        $user->get(2, 'id');
        // 可以查询到获取该列的其他数据
        echo $user->name;
        // 也可以直接给对象属性赋值后执行insert()插入数据
        $user->name = 'kicoe';
        $user->password = sha1('pa');
        $user->insert();
        // 当然insert可以像查询构造器那样传入参数
        $user->insert(['name','passwd'], [['kicoe',sha1('pa')], ['poi',sha1('pom')]] );
        // 使用set构造where, 要优雅？
        $user->set([['id', 'not between', [1, 5]], 'or', ['name','kicoe'], ['password',sha1('pa')]]);
        // set清空
        $user->set()->get(2);
        // update更新数据
        $user->update(['name'=>'k']);
        // select查询数据,默认查询所有
        $user->select('name');
        // delete删除数据，未使用get / set构造查询条件则会删除所有
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
// 返回上一条执行语句的id
lastInsertId()
// select的第二个参数可以返回以该参数为键的关联数组哦
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
    public function index($a = 'a', $b = 'b')
    {
        // 为页面赋值的两种方式
        $this->assign('a', $a);
        $this->assign(['a'=>$a, 'b'=>$b]);
        // 默认显示视图app/view/Index(控制器名)/index(操作名).php
        $this->show();
        // 当然也可以自定义，位于app/view/a/b.php
        $this->show('a/b');
    }
}
```
#### 关于Session和Request的操作
```php
<?php
namespace app\controller;

use \kicoe\Core\Controller;
use \kicoe\Core\Request;
use \kicoe\Core\Session;
use \kicoe\Core\File;

class Index extends Controller
{
    // 依赖注入 Request | $request = Request::getInstance();
    public function index(Request $request)
    {        
        // 获取用户post提交的name数据
        $name = $request->post('name');
        // 验证获取(post)
        $email = $request->validate('email', ['reg'=>'/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/']);
        $text = $request->validate('comment', ['len'=>444]);
        // session操作
        Session::set('name','kicoe');
        if (Session::has('name')) {
            echo Session::get('name');
        }
        // 返回文件操作类 (继承自SplFileInfo)
        $file = $request->file('file');
        // 验证
        $file->veri(['size'=>500, 'type'=>'text/css', 'ext'=>['css', 'txt']]);
        // 上传 @return SplFileInfo
        $file = $file->CP(PUB_PATH. 'static/img/i.jpg');
    }
}
```

## 完结撒花

花了一些时间，终于把这个小小的框架写好了

[blog](http://kicoe.com)
