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
     * 根据用户id查找用户信息
     */
    public function getUser(){
        return M('user')->where('status=1')->find();
    }


    /**
     * 根据用户id查找用户信息
     */
    public function getUserById($userid){
        return M('user')->where('id=%d',$userid)->find();
    }

    /**
     * 根据手机号码查找用户信息
     */
    public function getUserTelByTel($tel){
        return M('user')->where(" tel='%s' ",$tel)->find();
    }

    /***获取小编
     * @return mixed
     */
    public function getSalesmanUser(){
        return M('user')->where('roleid=3 AND status=1')->select();
    }

}


