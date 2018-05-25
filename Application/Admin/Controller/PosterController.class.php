<?php

namespace Admin\Controller;


class PosterController extends BaseController
{
    //海报列表页面
    public function index(){
        admin_check();
//        echo 123;exit();
        $this->display();
    }

    //获取海报列表
    public function getPosterList(){
        admin_check();
    }

    //新建海报
    public function addPoster(){
        admin_check();

        if (IS_POST){
            $post=array(
                "wx_ptc_name"            =>  I("wx_ptc_name","","trim"),
                "wx_ptc_start_time"      =>  I("wx_ptc_start_time","","trim"),
                "wx_ptc_end_time"        =>  I("wx_ptc_end_time","","trim"),
                "wx_ptc_status"          =>  I("wx_ptc_status","","trim"),
                "wx_ptc_wc_id"           =>  I("wx_ptc_wc_id","","trim"),
                "wx_create_time"         =>  date("Y-m-d H:i:s"),
            );
            $add=M('poster_config','wx_')->add($post);
            if ($add>0){
                $msg['status']=1;
                $msg['msg']="活动新建成功";
            }
            $this->ajaxReturn($msg);

//            show_bug($_POST);exit();
        }else{
            $wx_list = D("WechatAccount")->getWechatAccoun();
            $this->assign('wx_list',$wx_list);
            $this->display();
        }
    }


    //修改活动
    public function save(){
        $ptc_id = I("ptc_id",0);
        if ($ptc_id <= 0) $this->error("参数有误");

        $ptc_info = M('poster_config','wx_')->where('wx_ptc_id=%d',$ptc_id)->find(); //根据id获取公众号信息

        if (IS_POST){
            $post=array(
                "wx_ptc_name"            =>  I("wx_ptc_name","","trim"),
                "wx_ptc_start_time"      =>  I("wx_ptc_start_time","","trim"),
                "wx_ptc_end_time"        =>  I("wx_ptc_end_time","","trim"),
                "wx_ptc_status"          =>  I("wx_ptc_status","","trim"),
            );
            $save=M('poster_config','wx_')->save($post);
            if ($save>0){
                $msg['status']=1;
                $msg['msg']="活动修改成功";
            }
            $this->ajaxReturn($msg);

        }else{
            $this->assign('ptc_info',$ptc_info);
            $this->display();
        }
    }


    //删除活动
    public function delete(){
        admin_check();

        $ptc_id = I("ptc_id",0);
        if ($ptc_id <= 0) $this->error("参数有误");

        $delete = M('poster_config','wx_')->where('wx_ptc_id=%d',$ptc_id)->delete();
        $delete ? $this->success("删除成功") : $this->error("删除失败");
    }

}