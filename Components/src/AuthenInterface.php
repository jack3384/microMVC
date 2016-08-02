<?php

namespace jikai\Components;


interface AuthenInterface
{
    public function makeCode();
    public function verifyCode(array $info);

}