<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends BaseController {
    public function index(){
        $user_info = M('user')->field('username,u_wc_id')->where('id=%d',session('userid'))->find();
        $wc_info = M('wechat_account')->where('wc_id=%d',$user_info['u_wc_id'])->find();
        $this->assign('wc_info',$wc_info);
        $this->display();
    }



}