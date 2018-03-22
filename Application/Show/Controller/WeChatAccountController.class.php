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


}