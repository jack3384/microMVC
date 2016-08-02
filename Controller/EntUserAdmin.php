<?php

namespace jikai\Controller;

use jikai\Components\wechat\EntUser;
use jikai\microMVC\Controller;

class EntUserAdmin extends Controller
{
    public function __construct()
    {
        $this->filter('jikai\Filter\IsLogin');
    }

    public function beforeAction()
    {
        $username = isset($_SESSION['username'])?$_SESSION['username']:"访客";
        $this->layout("admin/layout",compact('username'));
    }

    public function userList()
    {
        $entUser=new EntUser();
        if(!empty($entUser->errorMsg)){
            $this->render("admin/error",["message"=>"有错误发生请检查企业号配置：{$entUser->errorMsg}"]);
            return;
        }
        $users=$entUser->listUser();
        $this->render("ent/userList",compact('users'));
        return;

    }

    public function addUser()
    {
        $rule=["mobile"=>"required|mobile",
        "name"=>"required"];
        if(isset($_POST['email'])&&$_POST['email']!=""){
            $rule['email']='email';
        }
        if(!$this->validate($_POST,$rule)) return $this->errorMsg;
        $data['userid']=$_POST['mobile'];
        $data['mobile']=$_POST['mobile'];
        $data['name']=$_POST['name'];
        $entUser=new EntUser();
        $res=$entUser->create($data);
        if($res){
            return "添加成功";
        }else{
            return "添加失败：".$entUser->errorMsg;
        }
    }

    public function editUser()
    {
        $rule=["userid"=>"required|mobile",
            "name"=>"required"];
        if(isset($_POST['email'])&&$_POST['email']!=""){
            $rule['email']='email';
        }
        if(!$this->validate($_POST,$rule)) return $this->errorMsg;
        $data['userid']=$_POST['userid'];
        $data['mobile']=$_POST['userid'];
        $data['name']=$_POST['name'];
        $data['email']=$_POST['email'];
        $data['weixinid']=$_POST['weixinid'];
        $entUser=new EntUser();
        $res=$entUser->update($data);
        if($res){
            return "更新成功";
        }else{
            return "更新失败：".$entUser->errorMsg;
        }
    }

    public function deleteUser()
    {
        $rule=["userid"=>"required|mobile"];
        if(!$this->validate($_POST,$rule)) return $this->errorMsg;
        $entUser=new EntUser();
        $res=$entUser->delete($_POST['userid']);
        if($res){
            return "删除成功";
        }else{
            return "删除失败：".$entUser->errorMsg;
        }
    }


}