<?php
namespace Wx\Controller;


class IndexController extends BaseController {
    public function index(){
//        include "../wechat.class.php";
//        import("Vendor.wechat-sdk.wechat");
//
        $options = array(
            'token'=>'gaoyintk', //填写你设定的key
            'encodingaeskey'=>'rIMsVWhv3uHAuyGPhIi2l1DXINb7g4xQFJJihiA8CII', //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid'=>'wxe0503ddb4a9efe4c', //填写高级调用功能的app id
            'appsecret'=>'25aa49db47d4ac396ea3cd727394eb6b' //填写高级调用功能的密钥
        );

        $weObj = new \Wechat($options);

        $access_token = $weObj->checkAuth();

        $check_access = $weObj->valid();
        echo $access_token;

//        $ca=$weObj->getCache('wechat_access_tokenwxe0503ddb4a9efe4c');
//        show_bug($access_token);


    }





}