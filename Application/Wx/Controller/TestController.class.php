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
        $user_info = $weObj->getUserInfo('oNaLl0xGdgHkdHVkfqA1QwJ2frM4'); //获取粉丝信息
        show_bug($user_info);exit();

        $rs = $this->transmitImg($weObj,'TEBRsBOkLMMtOOx_bx0QavGo8KtLfqXPKWPA0y4y8z4LChhZiaJ69rbFu6l4IlXn');
        return $rs;
    }


    //上传图片，获取media_id
    public function uploadMedia(){
        $options = array(
            'token' => 'jsgc', //填写你设定的key
            'encodingaeskey' => 'rIMsVWhv3uHAuyGPhIi2l1DXINb7g4xQFJJihiA8CII', //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => 'wxe0503ddb4a9efe4c', //填写高级调用功能的app id
            'appsecret' => '25aa49db47d4ac396ea3cd727394eb6b' //填写高级调用功能的密钥
        );
        $weObj = new \Wechat($options);

//        $tk = '10_iLdx0PKGJfitqTtAglW7oqZTaNHpy8Oe3RbgoU6cz71SbxvczBXFJl1tz6_mP22k8ymgcKREMaBT2b9-iCtGAMZPMM73HJLQjABI__YpUqTtvsSnEhJeQfgQR74LVVhACAFPP';


//        $path = "/Public/Uploads/poster/387406.jpg";//绝对路径，不可以是外链
        $img_url = $_SERVER['DOCUMENT_ROOT'].'/Public/Uploads/poster/387406.jpg'; //绝对路径，不可以是外链

        $data = array(
            'media' =>  '@'.$img_url
        );


//        $filepath = dirname ( __FILE__ ) . "/123.jpg";
//        echo $filepath;
        $up = $weObj->uploadMedia($data,'image');

        $media_id = $up['media_id'];
        show_bug($up);
        show_bug($weObj);

    }


    /*
     * 回复图片消息
     */
    private function transmitImg($object, $mediaId)
    {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[image]]></MsgType>
                    <Image>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Image>
                    </xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $mediaId);
        return $result;
    }

}