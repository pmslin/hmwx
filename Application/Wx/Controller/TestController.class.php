<?php

namespace Wx\Controller;


class TestController extends BaseController
{
    public function index(){

        $options = array(
            'token' => 'jsgc', //填写你设定的key
            'encodingaeskey' => 'rIMsVWhv3uHAuyGPhIi2l1DXINb7g4xQFJJihiA8CII', //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => 'wxe0503ddb4a9efe4c', //填写高级调用功能的app id
            'appsecret' => '25aa49db47d4ac396ea3cd727394eb6b' //填写高级调用功能的密钥
        );
        $weObj = new \Wechat($options);

        $dst_path  = 'http://'.$_SERVER['HTTP_HOST'].'/Public/Uploads/poster/387406.jpg';

        $newsData = array(
            'ToUserName'    =>  'oNaLl0xGdgHkdHVkfqA1QwJ2frM4',
            'FromUserName'  =>  'gh_32d7972c6152',
            'CreateTime'    =>  time(),
            'MsgType'       =>  'news',
            'ArticleCount'  =>  1,
            'PicUrl'        =>  $dst_path,

        );
        $img = $weObj->news($newsData);

//        $dst_path['media']  = 'http://'.$_SERVER['HTTP_HOST'].'/Public/Uploads/poster/387406.jpg';

//        echo $dst_path;
//        $up = $weObj->uploadForeverMedia($dst_path,'image');
//        echo 123;
        show_bug($img);
    }

}