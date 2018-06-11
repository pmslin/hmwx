<?php

namespace Wx\Controller;


class TestController extends BaseController
{
    public function index(){
        $poster_model = M('poster','wx_');
        $poster_info = $poster_model->where("wx_pt_wx_id=%d AND wx_pt_code='%s'",$wc_info['wc_id'],123456)->find();
        $sql=M()->_sql();
        echo $sql;
    }

}