<?php

namespace jikai\microMVC;


abstract class Model extends ORM
{
    public function __construct()
    {
        $class=explode("\\",get_called_class());
        $table=strtolower(array_pop($class));
        if(empty(($this->table))) $this->table=$table;
        $this->connect();
    }
}