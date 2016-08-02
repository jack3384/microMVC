<?php

namespace jikai\Components\wechat;


use jikai\Components\Curl;

class EntSendMsg
{
    protected $apiUrl="https://qyapi.weixin.qq.com/cgi-bin/message/send";
    protected $curl;

    public function __construct()
    {
        //这种多次调用就不会重复创建对象
        $this->curl=new Curl();
    }

    public function sendMsg($data)
    {
        $token=new EntAccessToken();
        $token1['access_token']=$token->getAccessToken();
        return $this->curl->post($this->apiUrl,$data,$token1);
    }

    public function sendTxt($content,array $user=array('@all'),$agentId=1)
    {
        $data=MsgFormater::sendTxt($content,$user,$agentId);
        $token=new EntAccessToken();
        $token1['access_token']=$token->getAccessToken();
        return $this->curl->post($this->apiUrl,$data,$token1);
    }

    public function sendNews(array $news,array $user=array('@all'),$agentId=1)
    {
        $data=MsgFormater::sendNews($news,$user,$agentId);
        $token=new EntAccessToken();
        $token1['access_token']=$token->getAccessToken();
        return $this->curl->post($this->apiUrl,$data,$token1);
    }

}