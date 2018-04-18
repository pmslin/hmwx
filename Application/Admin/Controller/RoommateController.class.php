<?php

namespace Admin\Controller;


use Admin\Model\WechatAccountModel;
use Model\RoommateConfigModel;

class RoommateController extends BaseController
{

    /**
     * 卖舍友数据列表页面
     */
    public function RoommateList(){
//        $list = D("LoveWall")->getNewLoveWall();
////        var_dump($list);exit();
//        $this->ajaxReturn($list,'json');

        $this->display();
    }

    /**
     * 获取卖舍友数据列表页面
     */
    public function getRoommateList(){
//        show_bug(I());exit();
        $userInfo = M("user")->where("id=%d",session("userid"))->find();

        $map=array();
        $map['rm_wc_id'] = $userInfo['u_wc_id'];
        $map['rm_new'] = 1;

        $data_b = I("date_b");
        $data_e = I("date_e");

        $map_data_b = empty($data_b) ? date("Y-m-d") : $data_b;
        $map_data_e = empty($data_e) ? date("Y-m-d") : $data_e;


        if (!empty($data_b) || !empty($data_e)){
            $map['rm_create_time']=array('between',array($map_data_b." 00:00:00",$map_data_e." 23:59:59"));
            $map['rm_new'] = 0;
        }


        $list = M("roommate")->where($map)->order("rm_sort DESC,rm_create_time ASC")->select();
//        echo M()->_sql();
        foreach ($list as $k=>$v){
            $list[$k]['i'] = $k+1;
            $list[$k]['i_id'] = $list[$k]['i'].'('.$v['rm_id'].')';
            $list[$k]['ac'] = '<button class="layui-btn" onclick="sort('.$v['rm_id'].')" >置顶</button> 
                                <button class="layui-btn" onclick="dele('.$v['rm_id'].')" >删除</button>';
            if (empty($v['rm_img_url'])){
                $list[$k]['img'] = "";
            }else{
                $list[$k]['img'] = "<img width='200' src='../../Public/".$v['rm_img_url']."'/>";
            }
        }


        //获取表白墙排版数据
        if (I("style")==1){
            $this->assign("list",$list);
//            show_bug($list);
            $this->display("style");
            exit();
        }

        //复制完成，将数据改变为旧数据
        if (I("copy")==1){
            if (empty($list)) $this->ajaxReturn("没有数据");
            $ids= implode(",",array_column($list,"rm_id"));
            $save_all = M('roommate')->where(" rm_id IN ($ids) ")->save(array("rm_new"=>0));
            $save_all !== false ? $this->ajaxReturn("完成") : $this->ajaxReturn("失败~");
            exit();
        }

        $this->ajaxReturn($list,'json');
    }


    /**
     * ajax置顶
     */
    public function sort(){
        $id = I("rm_id",0,"intval");
        $lw_model = M("roommate");
        $max_sort = $lw_model->where("rm_new=1")->max("rm_sort");
        empty($max_sort) ? $max_sort=100 : $max_sort;
        $sort = $max_sort + 1;
        $save = $lw_model->where("rm_id=%d",$id)->save(array("rm_sort" => $sort));
        $save == false ? $this->ajaxReturn("置顶失败")  : $this->ajaxReturn("置顶成功");
    }


    /**
     * ajax删除
     */
    public function delete(){
        $rm_id = I("rm_id",0,"intval");
        if ($rm_id <= 0) $this->ajaxReturn("参数错误");
        $delete = M("roommate")->where("rm_id = %d",$rm_id)->delete();
        $delete ? $this->ajaxReturn("删除成功") : $this->ajaxReturn("删除失败");
    }


    /**
     * 卖舍友设置
     */
    public function settings(){
        $userInfo = D("User")->getUserById(session('userid'));
        if ($userInfo['u_wc_id'] <= 0) $this->error("还未绑定所属公众号，请联系管理员绑定");

        $rmc_Model = M("Roommate_config");
        $rmc_info = $rmc_Model->where('rmc_wc_id=%d',$userInfo['u_wc_id'])->find();


        if (IS_POST){
            $post=I("post.");
//            show_bug($post);exit();

            $file = $this->upload();
            if (!empty($file)) $post['rmc_file'] ='Uploads/roommate/'.$file;

            $wc_id = I("wc_id",0);
            if ($wc_id <= 0) $this->error("参数有误");
            $rmc_info = RoommateConfigModel::getRoommateConfigById($wc_id);
            if (empty($rmc_info)){
                $post['rmc_wc_id'] = $wc_id;
                $add = $rmc_Model->add($post);
            }else{
                $add = $rmc_Model->where("rmc_id=%d",$rmc_info['rmc_id'])->save($post);
            }

//            show_bug($lwc_info);exit();

            $add !== false ? $this->success("保存成功") : $this->error("保存失败");

        }else{
            $wc_info = M("wechat_account")->where("wc_id=%d",$userInfo['u_wc_id'])->find();
            if (empty($wc_info)) $this->error('绑定的公众号被删除了，请重新联系管理员重新绑定');

            $com_name = $_SERVER["SERVER_NAME"].'/show/roommate/page/wechat/'.$wc_info['wc_code'];
            $this->assign("com_name",$com_name);
            $this->assign("wc_id",$userInfo['u_wc_id']);
            $this->assign("rmc_info",$rmc_info);
            $this->display();
        }
    }


    //上传封面
    public function upload(){
        $upload = new \Think\Upload();// 实例化上传类   开始
        $upload->maxSize = 45728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Public/Uploads/roommate/'; // 设置附件上传目录    // 上传文件
        $info = $upload->uploadOne($_FILES['rmc_file']); //pic为字段名
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