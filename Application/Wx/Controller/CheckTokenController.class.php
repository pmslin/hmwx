<?php

namespace Wx\Controller;


class CheckTokenController extends BaseController
{

    public function index()
    {
        if (!isset($_GET['echostr'])) { //发送消息
            $this->responseMsg();

        } else { //验证token
            $wc_id = I('get.wc_id', 0, 'intval');
            if ($wc_id <= 0) {
                echo 'wc_id有误';
                exit();
            }

            $wc_info = M('wechat_account')->where('wc_id=%d', $wc_id)->find();

            $options = array(
                'token' => $wc_info['wc_token'], //填写你设定的key
                'encodingaeskey' => $wc_info['wc_encodingaeskey'], //填写加密用的EncodingAESKey，如接口为明文模式可忽略
                'appid' => $wc_info['wc_appid'], //填写高级调用功能的app id
                'appsecret' => $wc_info['wc_appsecret'] //填写高级调用功能的密钥
            );
            $weObj = new \Wechat($options);
            $weObj->valid();

        }


    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        //print_r($GLOBALS);
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
//echo $RX_TYPE;
            //用户发送的消息类型判断
            switch ($RX_TYPE) {
                case "text":    //文本消息
                    $result = $this->receiveText($postObj);
                    break;
//                case "image":   //图片消息
//                    $result = $this->receiveImage($postObj);
//                    break;
//                case "voice":   //语音消息
//                    $result = $this->receiveVoice($postObj);
//                    break;
//                case "video":   //视频消息
//                    $result = $this->receiveVideo($postObj);
//                    break;
//                case "location"://位置消息
//                    $result = $this->receiveLocation($postObj);
//                    break;
//                case "link":    //链接消息
//                    $result = $this->receiveLink($postObj);
//                    break;
                default:
                    $result = "unknow msg type: " . $RX_TYPE;
                    break;
            }
            echo $result;
//            $result = $this->receiveText($postObj);
//            echo $result;
        } else {
            echo "kong";
            exit;
        }
    }


    private function img($object){
        $dateArray = array();
        $dateArray[] = array("Title"=>"单图文标题",
            "Description"=>"单图文内容",
            "Picurl"=>'http://'.$_SERVER['HTTP_HOST'].'/Public/Uploads/poster/387406.jpg',
            "Url" =>"http://baidu.com");
        $resultStr = $this->transmitNews($object, $dateArray);
        return $resultStr;
    }

    /*
     * 接收文本消息
     */
    private function receiveText($object)
    {
        //$weObj = new \Wechat($options);
        //$access_token = $weObj->getUserInfo('oNaLl0xGdgHkdHVkfqA1QwJ2frM4');
        //$data['test_cont'] = $access_token;


        $orginalid = '';
        $orginalid .= $object->ToUserName; //开发者微信号
        $options = $this->getOptions($orginalid);
        $weObj = new \Wechat($options);
        $from_user_name = '';
        $from_user_name .= $object->FromUserName; //发送方帐号（一个OpenID） 粉丝OpenID
        $user_info = $weObj->getUserInfo($from_user_name); //获取粉丝信息

        $wc_info = $this->getWC($orginalid); //获取公众号信息

        $content = '';
        $content .= $object->Content; // 接收的内容

        //回复图片
//        $rs = $this->transmitImg($object,'TEBRsBOkLMMtOOx_bx0QavGo8KtLfqXPKWPA0y4y8z4LChhZiaJ69rbFu6l4IlXn');
//        return $rs;


        if (is_numeric($content)) { //判断接收的内容是否是数字
            $poster_model = M('poster', 'wx_');
            $poster_info = $poster_model->where("wx_pt_wc_id=%d AND wx_pt_code='%s'", $wc_info['wc_id'], $content)->find(); //根据接收的助力码查找被助力的记录

            if (!empty($poster_info)) {
                $ptc_info = M('poster_config', 'wx_')->where('wx_ptc_id=%d', $poster_info['wx_pt_ptc_id'])->find();
                if ($ptc_info) {
                    $now_time = date('Y-m-d H:i:s');
                    if (!($ptc_info['wx_ptc_status'] == 1 && $now_time > $ptc_info['wx_ptc_start_time'] && $now_time < $ptc_info['wx_ptc_end_time'])) { //判断活动合法性
//                        $content = "活动已关闭，或不在活动时间";
//                        $result = $this->transmitText($object, $content);
//                        return $result;
                        exit();
                    }

                }

//            $content = "活动正常";
//            $result = $this->transmitText($object, $content);
//            return $result;
//            exit();


                $fans_model = M('fans', 'wx_');
                $fans_info = $fans_model->where("wx_fans_openid='%s' AND wx_fans_wc_id=%d", $from_user_name, $wc_info['wc_id'])->find();
                $fans_id = $fans_info['wx_fans_id'];

                if (empty($fans_info)) {
                    $fans_data = array(
                        'wx_fans_openid' => $from_user_name, // 粉丝OpenID
                        'wx_fans_name' => $user_info['nickname'], //粉丝名字
                        'wx_fans_wc_id' => $wc_info['wc_id'], //公众号的系统id
                    );
                    $fans_id = M('fans', 'wx_')->add($fans_data); //将粉丝信息添加至系统
                }



                //        $sql=M()->_sql();


                if ($poster_info['wx_pt_fans_id'] === $fans_id) { //如果助力者跟自己助力，提示无法帮自己助力
                    $content = "不能帮自己助力哦，赶紧叫朋友帮你助力吧。";
                    $result = $this->transmitText($object, $content);
                    return $result;
                }

                $check_poster = $poster_model->where('wx_pt_fans_id=%d AND wx_pt_ptc_id=%d',$fans_id,$poster_info['wx_pt_ptc_id'])->find();
                if ($check_poster){
                    $content = "你已经助力过了哦，赶紧叫朋友帮你助力吧。";
                    $result = $this->transmitText($object, $content);
                    return $result;
                }


                //生成助力信息
                $code = getCode(); //获取助力码

                createImg($ptc_info['wx_poster_url'],$ptc_info['wx_ptc_wc_qr'],$user_info['nickname'],$ptc_info['wx_ptc_name'],$code);//生成海报图片

                $img_url = $_SERVER['DOCUMENT_ROOT'].'/Public/Uploads/poster/'.$code.'.jpg'; //海报绝对路径，不可以是外链
                $data = array(
                    'media' =>  '@'.$img_url
                );
                $up_img = $weObj->uploadMedia($data,'image');

                if ($fans_id > 0) {
                    $poster_data = array(
                        'wx_pt_fans_id' => $fans_id,
                        'wx_pt_code' => $code,
                        'wx_pt_zl_id' => $poster_info['wx_pt_id'],
                        'wx_pt_wc_id' => $wc_info['wc_id'],
                        'wx_pt_openid' => $from_user_name,
                        'wx_pt_ptc_id' => $poster_info['wx_pt_ptc_id'],
                        'wx_pt_create_time' => date("Y-m-d H:i:s"),
                        'wx_pt_img_media_id' => $up_img['media_id'],
                    );
                    $add_poster = $poster_model->add($poster_data);

                    $content = "助力成功！恭喜你获得专属助力码：" . $code;
                    $result = $this->transmitText($object, $content);
                    return $result;

//                    $rs = $this->transmitImg($object,$up_img['media_id']);
//                    return $rs;
                }



//                $up = $weObj->uploadForeverMedia();
//                $content =$add_poster;
//                $result = $this->transmitText($object, $content);
//                return $result;

//            $data['test_cont'] = $add_poster;
//            M('test', 'wx_')->add($data);
//            exit();

//                if ($add_poster){
//                    $content = "助力成功！恭喜你获得专属助力码：" . $code;
//                    $result = $this->transmitText($object, $content);
//                    return $result;
//                }
            }
        } else {
            exit();
            $content = "无此内容";
            $result = $this->transmitText($object, $content);
            return $result;
        }
    }

    /*
     * 接收图片消息
     */
    private function receiveImage($object)
    {
        $content = "你发送的是图片，地址为：" . $object->PicUrl;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /*
     * 接收语音消息
     */
    private function receiveVoice($object)
    {
        $content = "你发送的是语音，媒体ID为：" . $object->MediaId;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /*
     * 接收视频消息
     */
    private function receiveVideo($object)
    {
        $content = "你发送的是视频，媒体ID为：" . $object->MediaId;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /*
     * 接收位置消息
     */
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，纬度为：" . $object->Location_X . "；经度为：" . $object->Location_Y . "；缩放级别为：" . $object->Scale . "；位置为：" . $object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /*
     * 接收链接消息
     */
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：" . $object->Title . "；内容为：" . $object->Description . "；链接地址为：" . $object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /*
     * 回复文本消息
     */
    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
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

    /*
     * 回复图文消息
     */
    private function transmitNews($object, $arr_item, $flag = 0)
    {
        if(!is_array($arr_item))
            return;

        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
    ";
            $item_str = "";
            foreach ($arr_item as $item)
                $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['Picurl'], $item['Url']);

            $newsTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[news]]></MsgType>
    <Content><![CDATA[]]></Content>
    <ArticleCount>%s</ArticleCount>
    <Articles>
    $item_str</Articles>
    <FuncFlag>%s</FuncFlag>
    </xml>";

        $resultStr = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item), $flag);
        return $resultStr;
    }

    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

//    public function responseMsg()
//    {
//        //get post data, May be due to the different environments
//        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
//
//        //extract post data
//        if (!empty($postStr)){
//
//            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
//            $fromUsername = $postObj->FromUserName;
//            $toUsername = $postObj->ToUserName;
//            $keyword = trim($postObj->Content);
//            $time = time();
//            $textTpl = "<xml>
//							<ToUserName><![CDATA[%s]]></ToUserName>
//							<FromUserName><![CDATA[%s]]></FromUserName>
//							<CreateTime>%s</CreateTime>
//							<MsgType><![CDATA[%s]]></MsgType>
//							<Content><![CDATA[%s]]></Content>
//							<FuncFlag>0</FuncFlag>
//							</xml>";
//            if(!empty( $keyword ))
//            {
//                $msgType = "text";
//                $contentStr = "Welcome to wechat world!";
//                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
//                echo $resultStr;
//            }else{
//                echo "Input something...";
//            }
//
//        }else {
//            echo "";
//            exit;
//        }
//    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = 'wxtk';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    private function getOptions($orginalid)
    {
        $info = M('wechat_account')->where("wc_orginalid='%s'", $orginalid)->find();

        $options = array(
            'token' => $info['wc_token'], //填写你设定的key
            'encodingaeskey' => $info['wc_encodingaeskey'], //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => $info['wc_appid'], //填写高级调用功能的app id
            'appsecret' => $info['wc_appsecret'] //填写高级调用功能的密钥
        );

        return $options;
    }

    private function getWC($orginalid)
    {
        $info = M('wechat_account')->where("wc_orginalid='%s'", $orginalid)->find();

        return $info;
    }

}