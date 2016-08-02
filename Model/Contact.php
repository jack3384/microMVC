<?php

namespace jikai\Model;

use jikai\microMVC\Model;

class Contact extends Model
{
    protected $table='ent_user';//可通过设置 修改绑定的表名
    protected $primaryKey = 'mobile';

}