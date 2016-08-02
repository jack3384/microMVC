<?php

namespace jikai\microMVC;

class Router
{
    protected $method;
    protected $uri;
    protected $controller;
    protected $action;
    protected $argu = array();

    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        if (isset($_GET['route'])) {
            $route = explode('/', $_GET['route']);
            if (count($route) == 2) {
                $this->controller = $route[0];
                $this->action = empty($route[1])?"index":$route[1];
            } else {
                throw new \Exception("请正确输入控制器与方法名", 404);
                // trigger_error("请正确输入控制器与方法名",E_USER_ERROR);
                //报错代码
            }
            if (!empty($_GET['argu'])) {
                $this->argu = explode('/', $_GET['argu']);
            }
        } else {
            //没有设置控制器名都默认都导入到首页
            $this->controller = "Index";
            $this->action = "index";
            //throw new \Exception("请正确输入控制器与方法名", 404);
        }
    }


    public function run()
    {
        $fullClass = "\\jikai\\Controller\\" . $this->controller;
        if (!class_exists($fullClass)) {
            throw new \Exception("控制器文件不存在" . $fullClass, 404);
            //trigger_error("控制器文件不存在".$fullClass,E_USER_ERROR);
        }

        if (!method_exists($fullClass, $this->action)) {
            throw new \Exception($this->action."方法不存在" . $fullClass, 404);
        }


        //实例化控制器类
        $controller = new $fullClass;

        //通过反射可以在实例化前进行类的属性检查判断逻辑操作
        $reflect=new \ReflectionObject($controller);

        //控制反射类储存起来方便其他时候调用，目前用到wechat组件里。
        $GLOBALS['ReflectController']=$reflect;
        $GLOBALS['Controller']=$controller;


        /*执行过滤器，过滤器执行时控制器类已经实例化，所以构造和析构函数会生效*/
        //获取全局过滤器
        $filters = Factory::getConfig('filter')->toArray();
        //获取控制器自定义过滤器规则并与全局合并
        if (property_exists($controller, 'filter')) {
            foreach ($controller->filter as $filter => $operation) {
                $operation = explode(":", $operation);
                switch ($operation[0]) {
                    case 'enable':
                        $filters[] = $filter;
                        break;
                    case 'disable':
                        $key1 = array_search($filter, $filters);
                        unset($filters[$key1]);
                        break;
                    case 'only':
                        $key1 = array_search($filter, $filters);
                        if($key1!==false){
                            unset($filters[$key1]);
                        }
                        $actions = explode("|", $operation[1]);
                        if(array_search($this->action,$actions)!==false) {
                            $filters[] = $filter;
                        }
                        break;
                    case 'except':
                        $key1 = array_search($filter, $filters);
                        if($key1!==false){
                            unset($filters[$key1]);
                        }
                        $actions = explode("|", $operation[1]);
                        if(array_search($this->action,$actions)===false) {
                            $filters[] = $filter;
                        }
                        break;
                }
            }
        }
        //执行过滤器
        foreach ($filters as $key => $filter) {
            $this->filter(new $filter());
        }

        //如果定义了beforeAction先执行它
        if(method_exists($controller,"beforeAction")){
            $controller->beforeAction();
        }

        $outInfo=call_user_func_array(array($controller, $this->action), $this->argu);
        if(is_string($outInfo)){
            echo $outInfo;
        }elseif(is_array($outInfo)){
            header('Content-type: application/json');
            echo json_encode($outInfo);
        }else{
           //没有返回的时候，$outInfo=false
        }

    }

    //过滤器执行逻辑,过滤器一般在Filter目录下实现 FilterInterface接口
    protected function filter(FilterInterface $filter)
    {
       $filter->handle();
    }


}