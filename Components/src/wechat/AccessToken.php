<?php
namespace jikai\Components\wechat;

use jikai\microMVC\Factory;
use jikai\Components\Curl;

abstract class AccessToken{
    protected $apiUrl;//需要实现
    protected $appID;//需要实现
    protected $queryString=array();
    protected $accessToken;
    protected $expireTime=3600;
    public $errorMsg;

    abstract public function __construct();

    protected function setCache()
    {
        $cache=Factory::Cache();
        $cache->set($this->appID.'access_token',$this->accessToken,$this->expireTime);
    }

    protected function getCache()
    {
        $cache=Factory::Cache();
        return $cache->get($this->appID.'access_token');
    }

    public function getAccessToken()
    {
        $token=$this->getCache();
        if(!$token) $token=$this->requestAccessToken();
        return $token;
    }

    public function requestAccessToken()
    {
        $curl=new Curl();
        $res=$curl->get($this->apiUrl,$this->queryString);
        if($res){
            $data=json_decode($res,true);
            if(isset($data['access_token'])){
                $this->accessToken=$data['access_token'];
                $this->expireTime=$data['expires_in']/2;
                $this->setCache();
                return $this->accessToken;
            }elseif(isset($data['errcode'])){
                $this->errorMsg="errcode:".$data['errcode']." errMsg:".$data['errmsg'];
                //throw new \Exception("Get access_token failed! ".$this->errorMsg);
                return false;
            }
        }else{
            throw new \Exception($curl->error_message);
        }
        unset($curl);
    }
}