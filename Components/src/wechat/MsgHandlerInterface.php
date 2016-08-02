<?php

namespace jikai\Components\wechat;

interface MsgHandlerInterface
{
    public function getMsgArray();
    public function responseMsg(array $msg);
}