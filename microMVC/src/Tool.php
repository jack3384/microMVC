<?php

namespace jikai\microMVC;


class Tool
{
    static public function redirect($uri)
    {
        ob_clean();
        $url = str_replace("\\", "", dirname($_SERVER['PHP_SELF']));
        header("Location: {$url}/{$uri}");
        exit;
    }

    static public function echoJson(array $data){
        ob_clean();
        header("Content-Type:application/json;charset=UTF-8");
        echo json_encode($data);
        exit;
    }

    static public function bcrypt($pass){
        if(strlen($pass)<2) throw new \Exception("密码长度小于2位",200);
       return md5(crypt($pass,substr($pass,0,2)));
    }

    static public function startSession()
    {
        if(!isset($_SESSION)){
            session_start();
        }
    }

}