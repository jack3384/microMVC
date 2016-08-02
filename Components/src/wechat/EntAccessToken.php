<?php

namespace jikai\Components\wechat;

class EntAccessToken extends AccessToken
{
    public function __construct($corpId=null,$secret=null)
    {
        $c=$GLOBALS['ReflectController'];
        if($c->hasProperty("corpId")){
            $corp=$c->getProperty("corpId");
            if(!$corp->isPublic()) $corp->setAccessible(true);
            $corpId=$corp->getValue($GLOBALS['Controller']);
        }

        if($c->hasProperty("secret")){
            $sec=$c->getProperty("secret");
            if(!$sec->isPublic()) $sec->setAccessible(true);
            $secret=$sec->getValue($GLOBALS['Controller']);
        }


        $this->apiUrl="https://qyapi.weixin.qq.com/cgi-bin/gettoken";//?corpid=id&corpsecret=secrect
        //获取配置
        $mpConfig= include __DIR__."/EntConfig.php";
        $this->queryString['corpId']=empty($corpId)?$mpConfig['corpId']:$corpId;
        $this->queryString['corpsecret']=empty($secret)?$mpConfig['corpsecret']:$secret;
        $this->appID=$this->queryString['corpId'];
    }
}