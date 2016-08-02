<?php

namespace jikai\Components;

class CheckHost implements LastErrorInterface
{

    protected $errorCode;
    protected $errorMsg;

    public function checkNetwork($host, $port = 80, $timeout = 2)
    {
        $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
        if (!$fp) {
            $this->errorCode = $errno;
            $this->errorMsg = $errstr;
            return false;
        }
        fclose($fp);
        return true;
    }

    /*
     * 接收各种协议http,tcp，ssh等支持的协议
     * input 为要写入的值，$except为期待输出的值
     * 如果简单的要获得一个http请求的返回值用file_get_contents()即可。
     */
    public function checkService($host, $input, $except, $port = 80, $timeout = 3)
    {
        $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
        if (!$fp) {
            $this->errorCode = $errno;
            $this->errorMsg = "network Error:" . $errstr;
            return false;
        }
        stream_set_timeout( $fp ,$timeout);//设置超时，后面的参数单位为秒
        fwrite($fp, $input);
        $out = ""; //接受输出
        while (!feof($fp)) {
            $out.= fread($fp, 1024);
            $status = stream_get_meta_data( $fp ) ;//检测是否超时
            if($status['timed_out']){
                $this->errorCode=500;
                $this->errorMsg="read buffer time out!";
                return false;
            }
        }
        fclose($fp);
        if ($out == $except) {
            return true;
        } else {
            $this->errorCode = 403;
            $this->errorMsg = "output not match except that your given!the output is :".$out;
            return false;
        }
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