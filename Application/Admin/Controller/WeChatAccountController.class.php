<?php

namespace Admin\Controller;


class WeChatAccountController extends BaseController
{

    /***
     * 生成账号
     */
    public function AddWechatAccount(){
        admin_check();

        if ($_POST){
            $post=array(
                "wc_name"  =>  I("wc_name","","trim"),
                "wc_code"  =>  I("wc_code","","trim"),
            );

            $WechatAccountModel=D('WechatAccount');
            $wc_name=$WechatAccountModel->getWCTelByName($post['wc_name']);
            $wc_code=$WechatAccountModel->getWCTelByCode($post['wc_code']);
//            echo M()->_sql();
//            show_bug($wc_name);exit();
            if (!empty($wc_name)){
                $msg['status']=99;
                $msg['msg']="该公众号名称已存在！";
            }elseif (!empty($wc_code)){
                $msg['status']=99;
                $msg['msg']="该公众号标识已存在！";
            } else{
                $post['wc_create_time']=date("Y-m-d H:i:s");
                $add=$WechatAccountModel->add($post);
                if ($add>0){
                    $msg['status']=1;
                    $msg['msg']="添加公众号成功";
                }
            }

            $this->ajaxReturn($msg);
        }else{
            $this->display();
        }
    }


    //公众号列表页面
    public function index(){

        $this->display();
    }

    //获取用户列表
    public function getWcList(){
        $list = D("WechatAccount")->getWechatAccoun();
        foreach ($list as $k=>$v){
            $list[$k]['key']=$k+1;
            $list[$k]['ac']='<button class="layui-btn" onclick="save('.$v['wc_id'].')" >修改</button> 
                <button class="layui-btn" onclick="dele('.$v['wc_id'].')" >删除</button> ';
        }

        $this->ajaxReturn($list,'json');
    }

    //修改公众号
    public function wcSaveIndex(){
        $wc_info = M('wechat_account')->where('wc_id=%d',I('wc_id'))->find(); //根据id获取公众号信息

        if ($_POST){ //修改公众号
            $post=array(
                "wc_id"     =>  I('wc_id'),
                "wc_name"  =>  I("wc_name","","trim"),
                "wc_code"  =>  I("wc_code","","trim"),
            );

            $WechatAccountModel=D('WechatAccount');
            $wc_name=$WechatAccountModel->getWCTelByName($post['wc_name']);
            $wc_code=$WechatAccountModel->getWCTelByCode($post['wc_code']);

//            echo M()->_sql();
//            show_bug($wc_name);exit();
            if (!empty($wc_name) && $wc_info['wc_id']!=I('wc_id')){
                $msg['status']=99;
                $msg['msg']="该公众号名称已存在！";
            }elseif (!empty($wc_code)  && $wc_info['wc_id']!=I('wc_id')){
                $msg['status']=99;
                $msg['msg']="该公众号标识已存在！";
            } else{
//                $post['wc_create_time']=date("Y-m-d H:i:s");
                $save=$WechatAccountModel->save($post);
                if ($save != false){
                    $msg['status']=1;
                    $msg['msg']="修改公众号成功";
                }else{
                    $msg['status']=1;
                    $msg['msg']="没有修改内容";
                }
            }

            $this->ajaxReturn($msg);
        }else{ //公众号列表页面

            $this->assign('wc_info',$wc_info);
            $this->display();
        }
    }


    /***
     * 删除账号
     */
    public function dele(){
        admin_check();

        $wc_id = I("wc_id",0);
        if ($wc_id <= 0) $this->error("参数有误");

        $delete = M('wechat_account')->where('wc_id=%d',$wc_id)->delete();
        $delete ? $this->success("删除成功") : $this->error("删除失败");
    }


}