<?php

namespace Admin\Controller;


use Admin\Model\WechatAccountModel;
use Model\LoveWallConfigModel;

class LoveWallController extends BaseController
{

    /**
     * 表白墙数据列表页面
     */
    public function loveWallList(){
//        $list = D("LoveWall")->getNewLoveWall();
////        var_dump($list);exit();
//        $this->ajaxReturn($list,'json');

        $this->display();
    }

    /**
     * 获取表白墙数据列表页面
     */
    public function getLoveWallList(){
        $list = D("LoveWall")->getNewLoveWall();
        foreach ($list as $k=>$v){
            $list[$k]['i'] = $k+1;
            $list[$k]['top'] = "";
            if (empty($v['lw_img_url'])){
                $list[$k]['img'] = "";
            }else{
                $list[$k]['img'] = "<img width='200' src='../../public/".$v['lw_img_url']."'/>";
            }

        }
//        var_dump($list);exit();
//        exit();
        $this->ajaxReturn($list,'json');

    }


    /**
     * 表白墙设置
     */
    public function settings(){
        $userInfo = D("User")->getUserById(session('userid'));
        if ($userInfo['u_wc_id'] <= 0) $this->error("还未绑定所属公众号，请联系管理员绑定");

        $lwc_Model = M("love_wall_config");
        $lwc_info = LoveWallConfigModel::getLoveWallConfigById($userInfo['u_wc_id']);

        if (IS_POST){
            $post=I("post.");
//            show_bug($post);exit();

            $file = $this->upload();
            if (!empty($file)) $post['lwc_file'] ='Uploads/wall/'.$file;

            $wc_id = I("wc_id",0);
            if ($wc_id <= 0) $this->error("参数有误");
            $lwc_info = LoveWallConfigModel::getLoveWallConfigById($wc_id);
            if (empty($lwc_info)){
                $post['lwc_wc_id'] = $wc_id;
                $add = $lwc_Model->add($post);
            }else{
                $add = $lwc_Model->where("lwc_id=%d",$lwc_info['lwc_id'])->save($post);
            }

//            show_bug($lwc_info);exit();


            $add !== false ? $this->success("保存成功") : $this->error("保存失败");

        }else{
            $com_name = WechatAccountModel::COM_NAME.'show/wall/page/wechat/'.M("wechat_account")->where("wc_id=%d",$userInfo['u_wc_id'])->find()['wc_code'];
            $this->assign("com_name",$com_name);
            $this->assign("wc_id",$userInfo['u_wc_id']);
            $this->assign("lwc_info",$lwc_info);
            $this->display();
        }



    }


    //上传封面
    public function upload(){
        $upload = new \Think\Upload();// 实例化上传类   开始
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Public/Uploads/wall/'; // 设置附件上传目录    // 上传文件
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