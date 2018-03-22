<?php

namespace Admin\Controller;


use Admin\Model\WechatAccountModel;

class LoveWallController extends BaseController
{

    /**
     * 表白墙数据列表
     */
    public function loveWallList(){
//        show_bug(session());exit();

        $this->display();
    }


    /**
     * 表白墙设置
     */
    public function settings(){
        $userInfo = D("User")->getUserById(session('userid'));
        if ($userInfo['u_wc_id'] <= 0) $this->error("还未绑定所属公众号，请联系管理员绑定");

        $lwc_Model = M("love_wall_config");
        $lwc_info = $lwc_Model->find();

        if (IS_POST){
            $post=I("post.");
//            show_bug($post);exit();

            $file = $this->upload();
            if (!empty($file)) $post['lwc_file'] ='Uploads/'.$file;

//            show_bug($post);exit();
            $add = $lwc_Model->where("lwc_id=%d",$lwc_info['lwc_id'])->save($post);
            $add !== false ? $this->success("保存成功") : $this->error("保存失败");

        }else{
            $com_name = WechatAccountModel::COM_NAME.'show/wall/'.M("wechat_account")->where("wc_id=%d",$userInfo['u_wc_id'])->find()['wc_code'];
            $this->assign("com_name",$com_name);

            $this->assign("lwc_info",$lwc_info);
            $this->display();
        }



    }


    public function upload(){
        $upload = new \Think\Upload();// 实例化上传类   开始
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Public/Uploads/'; // 设置附件上传目录    // 上传文件
        $info = $upload->uploadOne($_FILES['lwc_file']); //pic为字段名
//        show_bug($upload->getError());exit();
        if (empty($info)) {
            return "";
        }elseif (!$info){ // 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            return  $info['savepath'] . $info['savename'];  //上传成功，$data['pic'] pic为字段名  结束
        }
    }



}