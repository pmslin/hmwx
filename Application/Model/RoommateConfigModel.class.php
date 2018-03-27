<?php
namespace Model;
use Think\Model;

class RoommateConfigModel extends Model
{

    //根据id获取对应的公众号卖舍友配置
    public function getRoommateConfigById($id){
        return M('Roommate_config')->where("rmc_wc_id=%d",$id)->find();
    }

}