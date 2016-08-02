<?php

namespace jikai\microMVC;

class ExceptionHandler
{

    static public function handle(\Exception $e)
    {
        ob_clean();//清空前面的输出
        $code = $e->getCode()>=100?$e->getCode():500; //默认为500
        $msg = $e->getMessage();
        header("HTTP/1.1 {$code}");
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header('Content-Type: text/html; charset=UTF-8');
        //header("Location: http://www.example.com/");
        if (isset($GLOBALS['config']['debug'])&&(!empty($GLOBALS['config']['debug']))) {
            echo $msg . "<br>";
            echo $e->getFile() . ". line:" . $e->getLine();
            echo "<pre>";
            echo $e->getTraceAsString();
        }else{
            if($code!=500){
                echo $msg . "<br>";
            }else{
                echo "Something wrong! You are a good guy.";
            }
        }
        //记录log error_log($msg.$code,3,"path");
    }

}