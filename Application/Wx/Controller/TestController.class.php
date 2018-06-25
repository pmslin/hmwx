<?php

namespace Wx\Controller;


class TestController extends BaseController
{
    public function prompt(){
        $info = M('poster_config','wx_')->where('wx_ptc_wx_id=79')->find();
        echo $info['wx_poster_prompt'];
        exit();
    }


    /*
     *@$url string 远程图片地址
     *@$dir string 目录，可选 ，默认当前目录（相对路径）
     *@$filename string 新文件名，可选
     */
    function GrabImage($url, $dir='', $filename=''){
        if(empty($url)){
            return false;
        }
        $ext = strrchr($url, '.');
        if($ext != '.gif' && $ext != ".jpg" && $ext != ".bmp"){
            echo "格式不支持！";
            return false;
        }

        //为空就当前目录
        if(empty($dir))$dir = './';

        $dir = realpath($dir);
        //目录+文件
        $filename = $dir . (empty($filename) ? '/'.time().$ext : '/'.$filename);
        //开始捕捉
        ob_start();
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();
        $size = strlen($img);
        $fp2 = fopen($filename , "a");
        fwrite($fp2, $img);
        fclose($fp2);
        return $filename;
    }

    public function head(){
        $head = 'http://thirdwx.qlogo.cn/mmopen/Hn2mrF1ksPCoO9WYTKDkeTse90OIrFEJYPYtElibKL5ONorpvl2uWxsE5lAM53HPjmQEayItTmusL4BrOUpm6POoOoQkA2rBI/64.jpg';
        $up = './Public/Uploads/head/';
//        $this->GrabImage($head,$up,'1.jpg');
        $this->downloadImage($head,$up,'166.jpg');
    }

    public function downloadImage($url, $path='images/',$filename)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);

        saveAsImage($url, $file, $path,$filename);
    }

    private function saveAsImage($url, $file, $path,$filename)
    {
//        $filename = pathinfo($url, PATHINFO_BASENAME);
        $resource = fopen($path . $filename, 'a');
        fwrite($resource, $file);
        fclose($resource);
    }



    public function index(){
        exit();
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

    public function sendMsg(){
        $options = array(
            'token' => 'jsgc', //填写你设定的key
            'encodingaeskey' => 'rIMsVWhv3uHAuyGPhIi2l1DXINb7g4xQFJJihiA8CII', //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => 'wxe0503ddb4a9efe4c', //填写高级调用功能的app id
            'appsecret' => '25aa49db47d4ac396ea3cd727394eb6b' //填写高级调用功能的密钥
        );
        $weObj = new \Wechat($options);

        $msg_data = array();
        $msg_data['touser'] = 'oNaLl0xGdgHkdHVkfqA1QwJ2frM4';
        $msg_data['msgtype'] = "text";
        $msg_data['text']['content'] = "Hello World";

//        $data['test_cont'] = json_encode($msg_data);
//            M('test', 'wx_')->add($data);
//            exit();

//        $result = $weObj->sendCustomMessage(json_encode($msg_data));
        $result = $weObj->sendCustomMessage($msg_data);
        show_bug($result);
        return $result;
        exit();
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