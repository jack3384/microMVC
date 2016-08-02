<?php
require_once 'vendor/autoload.php';

set_exception_handler(array('\jikai\microMVC\ExceptionHandler', 'handle')); //设置异常处理器
set_error_handler(array('\jikai\microMVC\ErrorHandler', 'handle'));//设置错误处理类
$config = new \jikai\microMVC\Config(); //注册配置为全部函数
$r = new \jikai\microMVC\Router(); //获得路由实例
$r->run(); //go!!!

