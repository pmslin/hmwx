<?php

namespace Admin\Controller;


class PosterController extends BaseController
{
    //海报列表页面
    public function index(){

//        echo 123;exit();
        $this->display();
    }

    //获取海报列表
    public function getPosterList(){

    }

    //新建海报
    public function addPoster(){
        if (IS_POST){

        }else{
            $this->display();
        }


    }

}