<?php

namespace Admin\Model;

use Think\Model;

class LoveWallModel extends Model
{
    /**
     * 获取新表白墙数据
     */
    public function getNewLoveWall(){
        return M("love_wall")->where("lw_new = 1")->order("lw_create_time asc")->select();
    }

}