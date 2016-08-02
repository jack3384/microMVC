##需求
PHP>=5.4
jikai/microMVC：主要是用到了其中工厂类与缓存类，或者自己修改下就不用这个依赖。
Ent开头的文件未企业号相关，Mp为公众号相关
公众号提供简单功能：完整的建议用overtrue/wechat
企业号还是提供简单功能：人懒
##使用
EntConfig.php 配置企业号相关信息
MpConfig.php 配置公众号相关信息

##获取access_token
```
$token=new EntAccessToken();
$access_token=$token->getAccessToken();

//会用到FileCache组件 保存token到缓冲时间为有效期的一半，token->requestAccessToken()就是不用缓存，去直接请求。
```

##回复用户消息
```
$a=new MpMsgHandler(); //构造函数会自动解析微信Post过来的内容
//getMsgArray方法可以获得完整的数组

//安装微信的格式格式化数组
$b=[['Title'=>'测试1','Description'=>'测试1','PicUrl'=>'','Url'=>'www.163.com'],
['Title'=>'测试2','Description'=>'测试2','PicUrl'=>'','Url'=>'www.baidu.com'] ];

//将数组格式化成微信要的格式,回复消息目前支持text,news2种，与主动回复不同
$msg=MsgFormater::news($b);
//$msg="收到消息";
//$msg=MsgFormater::text($msg);

//将格式化的东西返回给微信
 $a->responseMsg($msg);
 
 
 /*-----企业号实现相同-----*/
 $obj=new EntMsgHandler();
 $input=$obj->getMsgArray();
 $msg="收到消息";
 $msg=MsgFormater::text($msg);
 $obj->responseMsg($msg);

```

##企业号主调发送消息
```
 $ent=new EntSendMsg();
 
//所有函数最后省略的2个参数是 $user=数组要发给谁，$agentID
  $return=$ent->sendMsg($data);// 发送任意格式消息 $msg=MsgFormater::text($msg);
  
  //$return=微信的json返回
  
 $return=$ent->sendTxt("测试");
 $return=$ent->sendNews($news);
```

##用户管理
```
EntUser.php 实现了简单的用户的增删改查和oauth鉴权
建议查看 Contact 控制器文件。
```

####附录企业号首次认证
```
    public function test(){

// 假设企业号在公众平台上设置的参数如下
        $encodingAesKey = "你的信息";
        $token = "你的信息";
        $corpId = "你的信息";//需要更换
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
    }

```