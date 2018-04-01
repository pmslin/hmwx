<?php

namespace Show\Controller;


//use Show\Controller\BaseController;

use Model\RoommateConfigModel;

class RoommateController extends BaseController
{
    //表白墙页面
    public function page(){
        //根据url传值获取公众号标识，以此来确定进入的是哪个公众号
        $wc = D("Admin/WechatAccount")->getWCTelByCode(I("wechat","","trim"));
        if (empty($wc)) $this->error("迷路了？");

        $RMInfo =  RoommateConfigModel::getRoommateConfigById($wc['wc_id']);
        if (empty($RMInfo)) $this->error("迷路了？");
//echo M()->_sql();exit();
        $this->assign('rminfo',$RMInfo);
        $this->assign("wechat",I("wechat",'','trim'));
        $this->display();
    }

    //添加表白
    public function sendRoommate(){
        if (IS_POST){
            $post = array(
                "rm_tel"            =>  I("rm_tel","","trim"),
                "rm_nickname"       =>  I("rm_nickname","","trim"),
                "rm_grade"          =>  I("rm_grade","","trim"),
                "rm_wc_number"      =>  I("rm_wc_number","","trim"),
                "rm_introduce"      =>  I("rm_introduce","","trim"),
                "rm_interest"       =>  I("rm_interest","","trim"),
                "rm_wc_id"          =>  I("wechat",0),
                "rm_create_time"    =>  date("Y-m-d H:i:s")
            );

            $file = $this->upload();
//            show_bug($file);exit();
            if (!empty($file)) $post['rm_img_url'] ='Uploads/roommate/'.$file;

            $wc = D("Admin/WechatAccount")->getWCTelByCode(I("wechat","","trim"));
            $post['rm_wc_id'] = $wc['wc_id'];
//            show_bug($wc);exit();

            $add = M("Roommate")->add($post);
            $add  ? $this->success("提交成功") : $this->error("提交失败，请稍后再试");

        }
    }


    //上传图片
    public function upload(){
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = './Public/Uploads/roommate/';

        if ($_FILES['rm_img_url']['error']==4) return'';

        $info = $upload->uploadOne($_FILES['rm_img_url']);
        if (!$info) {

            echo($upload->getError());
        } else {
            return $info['savepath'] . $info['savename'];
        }
    }

}