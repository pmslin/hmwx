<?php
namespace Admin\Model;
use Think\Model;
class UserModel extends Model {

//    protected $tableName = 'user';

    const USER_ROLE_ID1=1;
    const USER_ROLE_ID2=2;
    const USER_ROLE_ID3=3;

    const USER_ROLE_NAME1="超级管理员";
    const USER_ROLE_NAME2="添加账号管理员";
    const USER_ROLE_NAME3="小编";


    /**
     * 根据用户id查找用户信息和所属公众号
     */
    public function getUserAndWC(){
        return M('user as u')
            ->field("id,username,tel,wc_name")
            ->join("wechat_account as wc ON wc.wc_id=u.u_wc_id","left")
            ->where('status=1')
            ->select();
    }


    /**
     * 根据用户id查找用户信息
     */
    public function getUserById($userid){
        return M('user')
            ->field("id,tel,username,u_wc_id")
            ->where('id=%d',$userid)
            ->find();
    }

    /**
     * 根据手机号码查找用户信息
     */
    public function getUserTelByTel($tel){
        return M('user')
            ->field("id,tel,username,u_wc_id")
            ->where(" tel='%s' ",$tel)
            ->find();
    }

    /***获取小编
     * @return mixed
     */
    public function getSalesmanUser(){
        return M('user')
            ->where('roleid=3 AND status=1')
            ->select();
    }


    /**
     * 删除用户
     */
    public function deteleById($userId){
        return M("User")->where("id=%d",$userId)->delete();
    }

}


