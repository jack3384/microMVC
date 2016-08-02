<?php

namespace jikai\Controller;

use jikai\Components\wechat\EntMsgHandler;
use jikai\Components\wechat\EntUser;
use jikai\Components\wechat\MsgFormater;
use jikai\Components\wechat\WXBizMsgCrypt;
use jikai\microMVC\Controller;
use jikai\microMVC\Factory;

class Contact extends Controller
{
    public function __construct()
    {
       // $this->filter('jikai\Filter\IsEntUser', 'only:addUser');
    }

    public function addUser()
    {
        if ($this->method() == "GET") {
            $this->layout("ent/layout");
            $this->render("ent/addEntUser");
        } elseif ($this->method() == "POST") {
            $rule = [
                "mobile" => "required|mobile",
                "name" => "required"
            ];
            if (isset($_POST['email']) && $_POST['email'] != "") {
                $rule['email'] = 'email';
            }
            if (!$this->validate($_POST, $rule)) {
                return $this->errorMsg;
            }
            $data['userid'] = $_POST['mobile'];
            $data['mobile'] = $_POST['mobile'];
            $data['name'] = $_POST['name'];
            $entUser = new EntUser();
            $res = $entUser->create($data);
            if ($res) {
                unset($data['userid']);
                $this->storeUser($data);
                return "添加成功";
            } else {
                return "添加失败：" . $entUser->errorMsg;
            }
        }
    }

    //查询联系人电话
    public function query()
    {
        $obj = new EntMsgHandler();
        $input = $obj->getMsgArray();
        if ($input['MsgType'] != "text") {
            die("success");
        }
        $name = $input['Content'];
        $db = Factory::DB();
        $res = $db->select()->from("ent_user")->where("name", 'like', "%{$name}%")->execute()->fetchAll();
        $msg = "";
        if (count($res) == 0) {
            $msg = "未找到联系人";
        } else {
            foreach ($res as $contact) {
                $msg .= $contact['name'] . "：" . $contact['mobile'] . PHP_EOL;
            }
        }
        $msg = MsgFormater::text($msg);
        $obj->responseMsg($msg);
    }

    protected function  storeUser(array $contact)
    {
        $model = new \jikai\Model\Contact();
        return $model->store($contact);
    }


    /*    //企业号首次验证使用
        public function test(){

    // 假设企业号在公众平台上设置的参数如下
            $encodingAesKey = "你的";
            $token = "你的";
            $corpId = "你的";//需要更换


            $sVerifyMsgSig =$_GET["msg_signature"];
            $sVerifyTimeStamp =$_GET["timestamp"];
            $sVerifyNonce = $_GET["nonce"];
            $sVerifyEchoStr = $_GET["echostr"];
    // 需要返回的明文
            $sEchoStr = "";
            $wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
            $errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
            if ($errCode == 0) {

                echo $sEchoStr;

            } else {
                print("ERR: " . $errCode . "\n\n");
            }
        }*/


}