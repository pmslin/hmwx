<?php
namespace Admin\Controller;
use Admin\Model\UserModel;
use Think\Controller;
class UserController extends BaseController {

    //用户列表页面
    public function index(){

        $this->display();
    }

    //获取用户列表
    public function getUserList(){
        $list = D("user")->getUser();

        echo 123;
        show_bug($list);exit();
        $this->ajaxReturn($list,'json');
    }


    /***
     * 生成账号
     */
    public function AddUser(){

        if ($_POST){
            $post=array(
                "username"  =>  I("username","","trim"),
                "tel"       =>  I("tel",0),
                "wechatAccount_id"  =>  I("wechatAccount_id",0)
            );

            $userModel=D('user');
            $checkTel=$userModel->getUserTelByTel($post['tel']);
            if (!empty($checkTel)){
                $msg['status']=99;
                $msg['msg']="该手机号码已被注册！";
            }else{
                $post['u_wc_id'] = $post['wechatAccount_id'];
                $post['password']=md5('123456');
                $post['roleid']=UserModel::USER_ROLE_ID3;//小编
                $post['createdtime']=date("Y-m-d H:i:s");
                $add=$userModel->add($post);
                if ($add>0){
                    $msg['status']=1;
                    $msg['msg']="添加账号成功";
                }
            }

            $this->ajaxReturn($msg);
        }else{
            $wcModel=D('wechat_account');
            $wechatAccount=$wcModel->getWechatAccoun();
            $this->assign('wechatAccount',$wechatAccount);
            $this->display();
        }
    }



}