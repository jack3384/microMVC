<?php

namespace jikai\Filter;

use jikai\microMVC\FilterInterface;
use jikai\microMVC\Tool;

class VerifyCsrf implements FilterInterface
{

    public function handle()
    {
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            Tool::startSession();
            if(!isset($_SESSION['csrfToken'])){
                throw new \Exception("Session里csrfToken没设置");
            }
            if((!isset($_POST['csrfToken']))||($_POST['csrfToken']!=$_SESSION['csrfToken'])){
                throw new \Exception("token验证失败,禁止访问",403);
            }
            unset($_SESSION['csrfToken']);//验证完后注销
        }
    }
}