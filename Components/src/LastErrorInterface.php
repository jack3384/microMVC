<?php

namespace jikai\Components;


interface LastErrorInterface
{
    /**
     * 实现该接口的对象，可以在调用其方法获得false后，通过调用接口方法获得额外的错误信息
     */
    public function getErrorCode();
    public function getErrorMsg();
}