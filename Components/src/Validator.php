<?php

namespace jikai\Components;


class Validator
{
    /**
     * @param array $rule
     * @param array $array
     * 格式如下
     * $filterRules=["aaa"=>"required|max:6"];
     * $array=["aaa"=>"水水水水水水水水"];
     * 新增验证规则新增一个函数即可
     * rule: required pattern notEmpty email mobile max min less
     */
    public $errorMsg;
    protected $key;

    public function validate(array $array,array $filterRules)
    {
        $this->key="";
        foreach ($filterRules as $key => $val) {
            if (!isset($key)) {
                continue;
            }
            if ($val == "") {
                continue;
            }
            if (!isset($array[$key])) {
                $this->errorMsg = "{$key}未设置";
                return false;
            }else{
                $this->key=$key;
            }
            $rules = explode("|", $val);
            foreach ($rules as $rule) {
                $rule = explode(":", $rule);
                if ($rule[0] == "") {
                    continue;
                }
                if (!method_exists($this, $rule[0])) {
                    continue;
                }
                if (isset($rule[1])) {
                    $status = $this->$rule[0]($array[$key], $rule[1]);
                } else {
                    $status = $this->$rule[0]($array[$key]);
                }
                if (!$status) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function required($val)
    {
        if (!empty($val)) {
            return true;
        }
        if($val==0){
            return true;
        }
        $this->errorMsg = "{$this->key}不能为空";
        return false;
    }

    protected function max($val, $maxLength)
    {
        $length=mb_strlen($val);
        if($length>$maxLength){
            $this->errorMsg = "{$this->key}长度：{$length}，超出最大长度：{$maxLength}";
            return false;
        }
        return true;
    }

    protected function min($val, $minLength)
    {
        $length=mb_strlen($val);
        if($length<$minLength){
            $this->errorMsg = "{$this->key}长度：{$length}，小于最小长度：{$minLength}";
            return false;
        }
        return true;
    }

    protected function mobile($val)
    {
        $pattern='/^1\d{10}$/';
        if(preg_match($pattern,$val)){
            return true;
        } else{
            $this->errorMsg = "{$this->key}需要11位的手机号码";
            return false;
        }
    }

    protected function email($val)
    {
        $pattern="/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if(preg_match($pattern,$val)){
            return true;
        } else{
            $this->errorMsg = "{$this->key}不是合法的Email地址";
            return false;
        }
    }

    protected function ip($val)
    {
        $pattern='/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/';
        if(preg_match($pattern,$val)){
            return true;
        } else{
            $this->errorMsg = "{$this->key}不是合法的IPv4地址";
            return false;
        }

    }

    protected function pattern($val,$pattern)
    {
        if(preg_match($pattern,$val)){
            return true;
        } else{
            $this->errorMsg = "{$this->key}不符合规则";
            return false;
        }
    }

    protected function between($val,$range)
    {
        $range=explode(",",$range);
        if(count($range)!=2) throw new \Exception("validator {$val}between语法错误:{$range}");
        if($range[0]>$val){
            $this->errorMsg = "{$this->key}小于最小值";
            return false;
        }
        if($range[1]<$val){
            $this->errorMsg = "{$this->key}大于最大值";
            return false;
        }
        return true;
    }

}