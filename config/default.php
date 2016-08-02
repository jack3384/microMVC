<?php
return array(
    'database'=>array('dsn' => "sqlite:" . dirname(__DIR__) . "/sqlite/mysqlite3.db",
                 'usr' => 'w', 'pwd' => 'wzzzz'),
    //多个数据库自定义一个数组即可
    'mysql_database'=>array('dsn' => 'mysql:host=your_db_host;dbname=your_db_name;charset=utf8',
                 'usr' => 'root', 'pwd' => '123'),
    'controllerPath' => dirname(__DIR__) . "/Controller",
    'viewPath' => dirname(__DIR__) . "/View",
    'cachePath' => dirname(__DIR__) . "/Cache",
    'debug' => 1 //0为不显示debug信息
);