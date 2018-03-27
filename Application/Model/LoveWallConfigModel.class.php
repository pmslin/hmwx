<?php
namespace Model;
use Think\Model;

class LoveWallConfigModel extends Model
{

    //根据id获取对应的公众号表白墙配置
    public function getLoveWallConfigById($id){
        return M('love_wall_config')->where("lwc_wc_id=%d",$id)->find();
    }

}