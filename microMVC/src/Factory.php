<?php

namespace jikai\microMVC;

use jikai\Components\FileCache;

class Factory
{
    static protected $config = array();
    static protected $db = array();
    static protected $cache=array();
    static protected $view;

    //需要连接其他数据库的自行 new \Slim\PDO\Database 文档在其目录下有
    static function DB($dbName='database')
    {
        if (!isset(self::$db[$dbName])) {
            self::$db[$dbName] = new \Slim\PDO\Database($GLOBALS['config'][$dbName]['dsn'], $GLOBALS['config'][$dbName]['usr'],
                $GLOBALS['config'][$dbName]['pwd']);
        }
        return self::$db[$dbName];
    }

    static function getConfig($file)
    {
        if (empty(self::$config[$file])) {
            self::$config[$file] = new \jikai\microMVC\Config($file);
        }
        return self::$config[$file];
    }

    //可以获取多个viewer通过缓冲ob_get_clean这样实现分层
    static function View()
    {
        return new \jikai\microMVC\Viewer($GLOBALS['config']['viewPath']);
    }

    static function ORM($table, $id = null,$option=array())
    {
        return new  \jikai\microMVC\ORM($table, $id,$option);
    }

    static function Auth()
    {
        return new SimpleAuth();
    }

    static function Cache($dir=null)
    {
        if(empty($dir)) $dir=$GLOBALS['config']['cachePath'];
        if(!isset(self::$cache[$dir])){
            self::$cache[$dir]= new FileCache($dir);
        }
        return self::$cache[$dir];
    }

}