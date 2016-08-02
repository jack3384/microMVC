<?php

namespace jikai\microMVC;


class ErrorHandler
{
    static public function handle($errno, $errmsg, $errfile, $errline)
    {
        $msg ="ERRNO:{$errno},MSG:{$errmsg}<br/>File:{$errfile},Line:{$errline}<br/>";
        throw new \Exception($msg,500);
    }

}