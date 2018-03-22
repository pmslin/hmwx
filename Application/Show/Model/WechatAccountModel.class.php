<?php
namespace Admin\Model;

use Think\Model;

class WechatAccountModel extends Model
{
    const COM_NAME="http://xiyaoju.cn/";

    /**
     * 获取公众号
     */
    public function getWechatAccoun(){
        return M('wechat_account')->where("wc_status=1")->select();
    }

    /**
     * 根据id获取公众号信息
     */
    public function getWechatAccounById($id){
        return M('wechat_account')->where('wc_id=%s',$id)->find();
    }

    /**
     * 根据名称查找公众号信息
     */
    public function getWCTelByName($name){
        return M('wechat_account')->where(" wc_name='%s' ",$name)->find();
    }

    /**
     * 根据标识查找公众号信息
     */
    public function getWCTelByCode($code){
        return M('wechat_account')->where(" wc_code='%s' ",$code)->find();
    }







}


