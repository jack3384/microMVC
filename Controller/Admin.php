<?php
namespace jikai\Controller;

use jikai\microMVC\Controller;
use jikai\microMVC\Factory;
use jikai\microMVC\Tool;
use jikai\Model\User;

class Admin extends Controller
{
    protected $userRule = array(
        'username' => 'required',
        'password' => 'required|min:6',
        'level' => 'required|between:0,10'
    );

    public function __construct()
    {
        $this->filter('\jikai\Filter\VerifyCsrf', 'only:login');
        $this->filter('\jikai\Filter\IsLogin', 'except:login|logout');
        $this->filter('\jikai\Filter\IsAdmin', 'only:userList|addUser|deleteUser|editUser');
        $this->layout("admin/layout");
    }

    public function login()
    {
        $auth = Factory::Auth();
        if ($this->method() == "GET") {
            if ($auth->isGuest()) {
                $this->renderPart('admin/login');
                return;
            } else {
                $this->doLogin();
            }
        } elseif ($this->method() == "POST") {
            $user = $auth->auth($_POST['username'], Tool::bcrypt($_POST['password']));
            if (!$user) {
                $this->renderPart('admin/login', array("errorMsg" => "账号密码错误"));
            } else {
                $this->doLogin();
            }
        } else {
            throw new \Exception("未知method");
        }
    }

    public function logout()
    {
        $auth = Factory::Auth();
        $auth->logout();
        $this->redirect("Admin/login");
    }

    public function index()
    {
        $username = $_SESSION['username'];
        $this->render('admin/index', compact('username'));
    }

    public function userList()
    {
        $username = $_SESSION['username'];
        $userInfo = new User();
        $users = $userInfo->findAll();
        $this->render('admin/userList', compact('users', 'username'));
    }

    public function addUser()
    {
        if(isset($_POST['email'])&&$_POST['email']!=""){
         $this->userRule['email']='email';
        }

        $res = $this->validate($_POST, $this->userRule);
        if (!$res) {
            return $this->errorMsg;
        }
        $user = new User();

        if ($user->findByFields(array('username' => $_POST['username']))) {
            return "用户已存在";
        }
        foreach ($_POST as $attr => $val) {
            $user->$attr = $val;
        }
        $user->password = Tool::bcrypt($_POST['password']);
        $user->save();
        return "添加成功";
    }

    public function deleteUser()
    {
        if ($this->method() != "POST") {
            return "GET访问受限";
        }
        if (!$_POST['id']) {
            return "信息不全";
        }
        $user = new User();
        if ($user->find($_POST['id'])) {
            if ($user->level >= 10) {
                return "权限不够";
            }
            $user->delete();
            return "删除成功";
        } else {
            return "删除失败";
        }
    }

    public function editUser()
    {
        if ($this->method() != "POST") {
            return "GET访问受限";
        }
        if(isset($_POST['email'])&&$_POST['email']!=""){
            $this->userRule['email']='email';
        }
        $rule = $this->userRule;
        $rule['id'] = "required";

        if ($_POST['password'] == "") {
            unset($rule['password']);
            unset($_POST['password']);
        }

        $res = $this->validate($_POST, $rule);
        if (!$res) {
            return $this->errorMsg;
        }

        $user = new User();
        if (!$user->find($_POST['id'])) {
            return "用户不存在";
        }
        foreach ($_POST as $attr => $val) {
            $user->$attr = $val;
        }
        if(isset($_POST['password'])){
            $user->password = Tool::bcrypt($_POST['password']);
        }
        $user->save();
        return "修改成功";

    }

    public function resetPassword()
    {
        if (!(isset($_POST['oldPassword']) && isset($_POST['newPassword']))) {
            return "信息不全";
        }
        $rule=array(
            'oldPassword' => 'required|min:6',
            'newPassword' => 'required|min:6'
        );

        $res = $this->validate($_POST, $rule);
        if (!$res) {
            return $this->errorMsg;
        }

        $users = new User();
        $users->find($_SESSION['id']) or die("用户信息有误");
        $oldPass = Tool::bcrypt($_POST['oldPassword']);
        if ($users->password === $oldPass) {
            $users->password = Tool::bcrypt($_POST['newPassword']);
            $users->save();
            return "修改成功";
        } else {
            return "原密码错误";
        }

    }

    protected function doLogin()
    {
        $this->redirect('Admin/index');
    }


}