<?php

namespace Show\Controller;


//use Show\Controller\BaseController;

use Model\LoveWallConfigModel;

class WallController extends BaseController
{
    //表白墙页面
    public function page(){
        //根据url传值获取公众号标识，以此来确定进入的是哪个公众号
        $wc = D("Admin/WechatAccount")->getWCTelByCode(I("wechat","","trim"));
        if (empty($wc)) $this->error("迷路了？");

        $LWInfo =  LoveWallConfigModel::getLoveWallConfigById($wc['wc_id']);
        if (empty($LWInfo)) $this->error("迷路了？");

        $this->assign('lwinfo',$LWInfo);
        $this->assign("wechat",I("wechat",'','trim'));
        $this->display();
    }

    //添加表白
    public function sendWall(){
        if (IS_POST){
            $post = array(
                "lw_express_class"  =>  I("lw_express_class","","trim"),
                "lw_express_content"    =>  I("lw_express_content","","trim"),
                "lw_your_name"      =>  I("lw_your_name","","trim"),
                "lw_link"           =>  I("lw_link","","trim"),
                "lw_wc_id"          =>  I("wechat",0),
                "lw_create_time"    =>  date("Y-m-d H:i:s")
            );

            $file = $this->upload();
//            show_bug($file);exit();
            if (!empty($file)) $post['lw_img_url'] ='Uploads/'.$file;

            $wc = D("Admin/WechatAccount")->getWCTelByCode(I("wechat","","trim"));
            $post['lw_wc_id'] = $wc['wc_id'];
//            show_bug($wc);exit();

            $add = M("love_wall")->add($post);
            $add  ? $this->success("表白成功") : $this->error("表白失败，请稍后再试");

        }
    }


    //上传图片
    public function upload(){
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = './Public/Uploads/';

        if ($_FILES['lw_img_url']['error']==4) return'';

        $info = $upload->uploadOne($_FILES['lw_img_url']);
        if (!$info) {

            echo($upload->getError());
        } else {
            return $info['savepath'] . $info['savename'];
        }
    }

}