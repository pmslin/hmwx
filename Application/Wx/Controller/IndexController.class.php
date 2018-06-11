<?php
namespace Wx\Controller;
use Think\Controller;
class IndexController extends BaseController {
    public function index(){
        $poster_model = M('poster','wx_');
        $poster_info = $poster_model->where("wx_pt_wc_id=%d AND wx_pt_code='%s'",79,123456)->find();
        $sql=M()->_sql();
        echo $sql;

//        include "../wechat.class.php";
//        import("Vendor.wechat-sdk.wechat");
//
        $options = array(
            'token'=>'jsgc', //填写你设定的key
            'encodingaeskey'=>'rIMsVWhv3uHAuyGPhIi2l1DXINb7g4xQFJJihiA8CII', //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid'=>'wxe0503ddb4a9efe4c', //填写高级调用功能的app id
            'appsecret'=>'25aa49db47d4ac396ea3cd727394eb6b' //填写高级调用功能的密钥
        );

        $weObj = new \Wechat($options);
//echo 123;
        // $access_token = $weObj->checkAuth();

        $check_access = $weObj->valid();

//        $ca=$weObj->getCache('wechat_access_tokenwxe0503ddb4a9efe4c');
        show_bug($access_token);


    }

    public function test(){
        $orginalid='gh_32d7972c6152';
        $wc_info = M('wechat_account')->where("wc_orginalid='%s'",$orginalid)->find();
        show_bug($wc_info);
        echo M()->_sql();exit();
        $options = array(
            'token'=>'jsgc', //填写你设定的key
            'encodingaeskey'=>'rIMsVWhv3uHAuyGPhIi2l1DXINb7g4xQFJJihiA8CII', //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid'=>'wxe0503ddb4a9efe4c', //填写高级调用功能的app id
            'appsecret'=>'25aa49db47d4ac396ea3cd727394eb6b' //填写高级调用功能的密钥
        );

        $weObj = new \Wechat($options);
        $user = $weObj->getUserInfo('oNaLl0xGdgHkdHVkfqA1QwJ2frM4');
        show_bug($user);

//        M('test','wx_')->add(array('test_cont'=>123));
    }





}