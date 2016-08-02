<?php
namespace jikai\Filter;

use jikai\microMVC\FilterInterface;
use jikai\microMVC\SimpleAuth;

class IsAdmin implements FilterInterface
{
    public function handle()
    {
        if(!isset($_SESSION)){
            session_start();
        }
        //需要admin权限的时候都直接访问数据库鉴权，而不用session里存的 保证安全
        $auth=new SimpleAuth();
        if(!$auth->isAdmin()){
          $message="只有管理员才能访问";
          $GLOBALS['Controller']->render("admin/error",compact('message'));
          exit;
        }
    }
}