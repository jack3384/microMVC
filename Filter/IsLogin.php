<?php

namespace jikai\Filter;
use jikai\microMVC\FilterInterface;
use jikai\microMVC\Tool;

class IsLogin implements FilterInterface
{
    public function handle()
    {
        if(!isset($_SESSION)){
            session_start();
        }
        if(!isset($_SESSION['isLogin'])||$_SESSION['isLogin']!=1){
            Tool::redirect("Admin/login");
        }
    }
}