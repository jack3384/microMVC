<?php

namespace jikai\Components;

class Authentication implements authenInterface,LastErrorInterface
{
    protected $key = "yourkey"; //salt for md5,各机器之间必须一样
    protected $timeInterval = 120;  //允许的最大时间间隔
    protected $errorCode;
    protected $errorMsg;

    /*可在类初始化时设置key
    public function __construct($key=null)
    {
        if($key!==null){
            $this->key=$key;
        }
    }
    */

    public function setTimeInterval($timeInterval)
    {
        $this->timeInterval = $timeInterval;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }


    /**
     * 根据key加密函数，返回时间戳随机数和加密后的code
     * @return array
     */
    public function makeCode()
    {
        $timestamp = time();
        $nonce = rand(10000, 99999);
        $temp = $this->key . $nonce . $timestamp;
        $code = md5($temp);
        return compact('timestamp', 'nonce', 'code');
    }

    /**
     * @param array $info
     * @return bool
     */
    public function verifyCode(array $info)
    {
        if(!(isset($info['timestamp'])&&isset($info['nonce'])&&isset($info['code']))){
            $this->errorCode="404";
            $this->errorMsg="timestamp|nonce|code at least miss one";
            return false; //时间紧就不抛出异常了
        }

        $timestamp =$info['timestamp'];
        $nonce = $info['nonce'];
        $code = $info['code'];

        if ($this->isTimeout($timestamp)) {
            $this->errorMsg="the code is outdated";
            return false;
        }
        $temp = $this->key . $nonce . $timestamp;
        return $code === md5($temp);
    }

    /**
     * @param $timestamp
     * @return bool
     * 验证是否超时
     */
    protected function isTimeout($timestamp)
    {
        return abs($timestamp - time()) > $this->timeInterval;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getErrorMsg()
    {
        return $this->errorMsg;
    }
}