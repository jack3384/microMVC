<?php

namespace jikai\Components\wechat;

class MpAccessToken extends AccessToken
{

    public function __construct($appid=null,$secret=null)
    {
        $c=$GLOBALS['ReflectController'];
        if($c->hasProperty("appid")){
            $corp=$c->getProperty("appid");
            if(!$corp->isPublic()) $corp->setAccessible(true);
            $appid=$corp->getValue($GLOBALS['Controller']);
        }

        if($c->hasProperty("secret")){
            $sec=$c->getProperty("secret");
            if(!$sec->isPublic()) $sec->setAccessible(true);
            $secret=$sec->getValue($GLOBALS['Controller']);
        }

        $this->apiUrl="https://api.weixin.qq.com/cgi-bin/token";//?grant_type=client_credential&appid=APPID&secret=APPSECRET"
        //获取配置
        $mpConfig= include __DIR__."/MpConfig.php";
        $this->queryString['grant_type']='client_credential';
        $this->queryString['appid']=empty($appid)?$mpConfig['appId']:$appid;
        $this->queryString['secret']=empty($secret)?$mpConfig['secret']:$secret;
        $this->appID=$this->queryString['appid'];
    }


}