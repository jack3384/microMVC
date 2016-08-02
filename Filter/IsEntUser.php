<?php

namespace jikai\Filter;

use jikai\Components\wechat\EntUser;
use jikai\microMVC\FilterInterface;
use jikai\microMVC\Tool;

class IsEntUser implements FilterInterface
{

    // 用户访问会这样的形式 redirect_uri?code=CODE&state=STATE
    public function handle()
    {
        Tool::startSession();
        if(isset($_SESSION['entUserID'])) return;
        if(!isset($_GET['code'])) {
            throw new \Exception("请点击微信上的连接进行访问",403);
        }
        $user=new EntUser();
        $res=$user->oauth($_GET['code']);
        if($res){
            $_SESSION['entUserID']=$res['UserId'];
            $_SESSION['entDeviceId']=$res['DeviceId'];
        }else{
            throw new \Exception("没有权限访问",403);
        }
    }
}