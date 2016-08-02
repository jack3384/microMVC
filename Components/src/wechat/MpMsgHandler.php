<?php

namespace jikai\Components\wechat;

class MpMsgHandler implements MsgHandlerInterface
{
    protected $responseFromUserName;
    protected $responseToUserName;
    protected $msgArray;

    public function __construct()
    {
        $postStr = self::getRawMsg();
        $postObj = simplexml_load_string($postStr,'SimpleXMLElement', LIBXML_NOCDATA);
        $msg=MsgFormater::objectToArray($postObj);
        $this->responseFromUserName=$msg['ToUserName'];
        $this->responseToUserName=$msg['FromUserName'];
        $this->msgArray=$msg;
    }

    static public function getRawMsg()
    {
        $sReqData = file_get_contents("php://input");
        /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
        the best way is to check the validity of xml by yourself */
        libxml_disable_entity_loader(true);
        return $sReqData;
    }

    /**
     * @return \SimpleXMLElement
     * 自动解密微信来的密文成xml字符串，并转换成对象
     */
    public function getMsgArray()
    {
        return $this->msgArray;
    }

    public function responseMsg(array $msg)
    {
        $msg['CreateTime']=time();
        $msg['FromUserName']=$this->responseFromUserName;
        $msg['ToUserName']=$this->responseToUserName;
        echo MsgFormater::xml($msg);
    }

}