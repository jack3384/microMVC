##前言

该微框架基于composer方便实现组件化应用。

框架定位：

- 该框架主要用于学习研究，语法糖不多，尽量采用的是PHP原生的方式，故相对于其他成熟框架更容易学习到MVC框架的基本运行原理
- 暂时未包含比较难理解的组件如：IOC容器、管道中间件等。
- 个人做小微系统开发：如微信公众号应用开发。
- 商城、CRM等大中系统推荐其他框架如laravel,YII2等

## 自带开箱即用用户管理系统
```
/Admin/login 登录 账号admin 密码123456
配置完微信企业号参数后可进行企业号用户管理
简单的企业号人员通讯录查询应用（输入名字--回复电话号码）
```

# microMVC
**基本用法：**
访问： `/index.php?route=控制器名/方法名&argu=参数1/参数2...`

.htacesss 做了规则重定向所以可以方便的访问
访问： `url/控制器名/方法名/[参数1/参数2]`

例如`127.0.0.1/Admin/login` 这个时候将不带参数

也可以 `127.0.0.1/Admin/login?getname=myname` 通过$_GET['getname']获得传递的数据

##配置文件
`config/default.php` 为默认配置文件
可配置控制器 Model等存放路径。已经是否显示debug信息和默认数据库
默认数据库为自带的sqlite3类型，可以将`database`信息替换成mysql的，
也可以多定义几个数据库连接信息后面model里会讲怎么用多数据库连接
```
'database'=>array('dsn' => 'mysql:host=your_db_host;dbname=your_db_name;charset=utf8','usr' => 'root', 'pwd' => 'pass'),
```
##控制器
```
未指定控制器与方法默认访问Index/index

//首先定义命名空间
namespace jikai\Controller;

//所有控制器应当继承框架的的Controller（其实是为了可以在自己控制器里用几个封装好的函数，如$this->render()）
use jikai\microMVC\Controller;
class Admin extends Controller
//or
class yourClass extends jikai\microMVC\Controller

//定义方法
public function yourfuc(){}
//这样就可以通过 127.0.0.1/yourClass/yourfuc 实现访问了

//or 定义带参数的方法
public function yourfuc($argu1,$argu2){}
//这样就可以通过 127.0.0.1/yourClass/yourfuc/参数1的值/参数2的值 实现访问了

//可以通过定义一下函数让控制器进行初始化操作（调用控制器方法前会执行，以做类的初始化操作）
public function __construct(){} //会先于Filter执行
public function  beforeAction(){} //在Filter之后执行



/*----------华丽的分界线-------------*/
//输出
return "字符串"; //输出字符串
return array(); //输出json

//视图函数
$this->render('admin/admin',array("name"=>"jack")) //就可以调用admin目录下admin.php视图，并把$name变量传递过去
$this-layout() //分层后面会将
$this->validate()//验证$_POST过来的函数是否合法等 后面会将
$this->redirect($url)//重定向请求

```
##Model
model层使用了slim的框架封装了一层ORM
可到`vendor\slim\pdo\docs`下查找其原生用法
下面说下封装后的ORM用法
```
namespace jikai\Model;
use jikai\microMVC\Model;
class User extends Model
//定义好命名空间继承自系统自带Model后就实现了一层ORM绑定
//默认绑定的表名与Model类名一致，如此例子 该MODEL绑定了table user(不区分大小写)

protected $table='tableName';//可通过设置 修改绑定的表名
protected $primaryKey = 'id'; //可通过设置 修改绑定的主键名默认为id
protected $dbName='database'; //可通过设置  修改绑定的数据库

实例化model后,可以实现orm功能
//插入新用户
$user= new User;
$user->name="myname"
$user->save();
//or修改老用户
$user->find(1); //1为主键id
$user->name="myname"
$user->save();

/*--------------获取批量数据-----------------*/

$user->findALL() //获得该表所有数组形式返回。默认最大前1000条。
$user->listALL($array) //按传入的字段名获得数据相当于只select 指定的字段 。
$user->findByFields($whereArray,$getALL=false)，

/** 使用方法：whereArray=['username'=>'value','password'=>'1233']这样形式传数组，表示where username='value' and password='1233'
* $getALL=true 表示返回所有，否则只返回一条
* 无数据$getALL=false返回的是false,有数据一维数组
* 加上$getALL=true无数据返回空数组，有数据二维数组 */

//更灵活的....
$user->findByWhere(array(['id','>',1],['username','=','jack']),$getALL=false)


//更多详细参考
jikai\microMVC\ORM.php

```

##视图
```
YII1.1类似语法
控制器$this->render("admin",array("key"=>"value"));
//控制器传递进去的数据键值即为视图里的变量名

<?php echo $this->root ?> //输出变量，$this->root表示输出当前的url根目录地址
//or
<?php echo $name ;?>  //也可以是控制器render()方法传递进去的变量

控制器里也可以使用layout
$this->layout('parent');
//父视图里需要嵌入子视图的地方加入这段代码<?php include $layout; ?>
//传入的变量 所有视图是共用的
$this->render('child');

```

##验证器validator
```
控制器里
$this->validate($要验证的数组,$验证规则数组)
//例如
$res=$this->validate($_POST,["aaa"=>"required|max:6"]);
//表示验验证$_POST['aaa'],他不能为空，最大6个字节。多个规则直接用|分隔
//其实每个规则都是jikai\Components\Validator 下的一个函数
     * 格式如下
     * $filterRules=["aaa"=>"required|max:6"];
     * $array=["aaa"=>"水水水水水水水水"];
     * 新增验证规则新增一个函数即可
     * 目前实现的rule有: required pattern IP email mobile max:长度 min:长度
```

##过滤器Filter
```
//调用Filter的时候控制器类已经实例化
可以在过滤器中通过 $GLOBALS['Controller'] 调用控制器对象。
//比如验证POST数据，登录状态，权限等 没通过可以重定向或者抛出错误而不执行该方法

/*
Filter定义 Filter目录下有个isLogin例子
所有Filter需要实现jikai\microMVC\FilterInterface;
如将该Filter定义到config/filter.php中 所有的控制器都将调用它（全局过滤器）
执行顺序为全局过滤器>控制器定义的过滤器，按定义的先后顺序执行。

也可以在控制器中通过构造函数调用 $this->filter()方法定义
*/

//会先于Filter执行，所以定义filter要在这里定义
public function __construct(){
   $this->filter($filter包含命名空间的全面，[动作=默认为enable])
   //例如
   $this->filter('jikai\Filter\IsLogin'） 启用该过滤器
   $this->filter('jikai\Filter\IsLogin','except:login|logout');
   //PS:请使用单引号，双引号会发生转义
   //表示处理login,logout方法 启用
   //还有 enable启用 disable禁用 only:login|logout 仅这些方法启用。
} 

```

##其他
**获得配置文件中定义的变量**
```
$GLOBALS['ReflectController'] //控制器反射类对象，可以在Filter、或其他组件中调用达到其他灵活的目的
$GLOBALS['Controller'] //控制器实例对象，可以在Filter、或其他组件中调用达到其他灵活的目的
$var=Factory::getConfig($file) //$file是文件名 默认为default 就会自动寻找 config/default.php 获得这个文件返回的数组
```
**文件缓存**
修改自jenner的文档
http://www.huyanping.cn/php%E6%96%87%E4%BB%B6%E7%BC%93%E5%AD%98%E5%AE%9E%E7%8E%B0/ 
```
$cache=Factory::Cache() //获得缓存内实例，系统会通过hash把缓存存到 Cache目录下
$cache=Factory::Cache();
$cache->set('key','value',60);
$cache->get('key'); //过期或不存在return false
/**
     * 功能实现：get、set、has、increment、decrement、delete、flush(删除所有)
     * DOC：new FileCache(缓存根目录名)
     * get($key) 获取值，set($key,$val,[$过期时间，不设置表示不过期]),increment($key,num)表示值+1或者自定义
     *
     *
     *为了避免一个文件内的数据过大，造成读取文件的时候延迟较高，我们采用一个key-value一个文件的方式实现存储结构。
     *为了支持key过期，我们需要把expire数据写入到文件中，所以需要对写入的数据进行序列化处理
     *为了能够快速的定位到文件路径，我们采用hash算法一次计算出文件位置
     * 避免过多缓存文件放在同一文件夹下，所以 参看path()函数
     * 根据md5($key)实现3级目录存放文件 例如： /home/b0/68/b068931cc450442b63f5b3d276ea4297
     * 缓存目录
     * @var
*/
```

**componets下 curl类**
modify by php-mod/curl https://github.com/php-mod/curl
```
public function get($url, $queryString = array())
public function post($url, $data = array(),$queryString=array())
//均返回获得的数据

```
##异常
```
/*有任何异常抛出或者错误警告都会中断脚本执行，抛出异常界面通常状态码为500,
如果config/default.php 中开启了debug将显示 异常信息与脚本执行的步骤等
入需要手动抛出异常脚本： */
throw new \Exception ("错误提示信息",500); //500为状态码可以修改比如404
```


