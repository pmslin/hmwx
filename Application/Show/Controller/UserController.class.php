<?php
namespace Admin\Controller;
use Admin\Model\UserModel;

class UserController extends BaseController {

    //用户列表页面
    public function index(){

        $this->display();
    }

    //获取用户列表
    public function getUserList(){
        $list = D("user")->getUserAndWC();
        foreach ($list as $k=>$v){
            $list[$k]['key']=$k+1;
            $list[$k]['ac']='<button class="layui-btn" onclick="save('.$v['id'].')" >修改</button> 
                <button class="layui-btn" onclick="dele('.$v['id'].')" >删除</button> ';
        }

        $this->ajaxReturn($list,'json');
    }


    /***
     * 账号修改
     */
    public function userSaveIndex(){
        admin_check();

        if (IS_POST){ //修改账号
            $post=array(
                "id"       =>  I("id",0),
                "username"  =>  I("username","","trim"),
                "tel"       =>  I("tel",0),
                "password"  =>  I("password","","trim"),
                "wechatAccount_id"  =>  I("wechatAccount_id",0)
            );

            $userModel=M('user');
            $checkTel=$userModel->where(" tel=%s AND id!=%d",$post['tel'],$post['id'])->find();

            if (!empty($checkTel)){
                $msg['status']=99;
                $msg['msg']="该手机号码已被注册！";
            }else{
                if (empty($post['password'])){
                    unset($post['password']);
                }else{
                    $post['password'] = md5($post['password']);
                }
                $post['u_wc_id'] = $post['wechatAccount_id'];

                $save=$userModel->where("id=%d",$post['id'])->save($post);
                if ($save !== false){
                    $msg['status']=1;
                    $msg['msg']="添加修改成功";
                }
            }

            $this->ajaxReturn($msg);

        }else{ //账号修改页面
            $user_id = I("user_id");
            if ($user_id <= 0) $this->error("参数有误");

            $userInfo = D("User")->getUserById($user_id);
            if (empty($userInfo)) $this->error("没有这个用户");

            $wcModel=D('wechat_account');
            $wechatAccount=$wcModel->getWechatAccoun();
            $this->assign('wechatAccount',$wechatAccount);

            $this->assign("userInfo",$userInfo);

            $this->display();
        }
    }




    /***
     * 生成账号
     */
    public function AddUser(){
        admin_check();

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


    /***
     * 删除账号
     */
    public function deleteUser(){
        admin_check();

        $user_id = I("id",0);
        if ($user_id <= 0) $this->error("参数有误");

        $delete = D("User")->deteleById($user_id);
        $delete ? $this->success("删除成功") : $this->error("删除失败");
    }



}