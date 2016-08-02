<?php

namespace jikai\microMVC;


class SimpleAuth
{
    protected $usernameKey;
    protected $passwordKey;
    protected $primaryKey;
    public $userIP;
    public $userClient;
    private $userORM;
    public $userInfo=false;

    public function __construct($table='user',$primaryKey='id',$usernameKey='username',$passwordKey='password')
    {
        if(!isset($_SESSION)){
            session_start();
        }
        $this->primaryKey=$primaryKey;
        $this->usernameKey=$usernameKey;
        $this->passwordKey=$passwordKey;
        $this->userORM=Factory::ORM($table);
        $this->userClient=$_SERVER['HTTP_USER_AGENT'];
        $this->userIP=$_SERVER['REMOTE_ADDR'];
    }

    /**
     * @param $username
     * @param $hashedPassword
     * @return mixed 失败false 成功一维数组
     *
     */
    public function auth($username,$hashedPassword)
    {
        $fields=array($this->usernameKey=>$username,$this->passwordKey=>$hashedPassword);
        $res=$this->userORM->findByFields($fields);
        if(!$res) return false;
        $this->userInfo=$res;
        $this->loginInit();
        return true;
    }

    public function login($username)
    {
        $fields=array($this->usernameKey=>$username);
        $this->userInfo=$this->userORM->findByFields($fields);
        if(!$this->userInfo) throw new \Exception("用户名{$username}不存在");
        $this->loginInit();
    }

    protected function loginInit()
    {
        $_SESSION['isLogin']=1;
        if(is_array($this->userInfo)){
            foreach($this->userInfo as $key=>$val){
                $_SESSION[$key]=$val;
            }
        }
        if($_SESSION[$this->passwordKey]) unset($_SESSION[$this->passwordKey]);
        $_SESSION['id']=$this->userInfo[$this->primaryKey];
        $_SESSION['username']=$this->userInfo[$this->usernameKey];
    }

    public function isAdmin()
    {
        if(!$this->userInfo){
            $fields=array($this->primaryKey=>$_SESSION['id']);
            $res=$this->userORM->findByFields($fields);
            if(!$res) return false;
            $this->userInfo=$res;
        }
        if(!isset($this->userInfo['level'])) return false;
        if(($this->userInfo['level'])>=10) return true;
        return false;
    }

    public function isGuest()
    {
        if(isset($_SESSION['isLogin'])&&$_SESSION['isLogin']==1) return false;
        return true;
    }

    public function logout()
    {
        $_SESSION['isLogin']=0;
        $_SESSION['id']=null;
        $_SESSION['username']=null;
        $this->userInfo=false;
        //something else to do
    }


}