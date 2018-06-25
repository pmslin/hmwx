<?php

namespace Admin\Controller;


class PosterController extends BaseController
{
    //海报列表页面
    public function index(){
        admin_check();

        $this->display();
    }

    //获取海报列表
    public function getPosterList(){
        admin_check();

        $list = M('poster_config','wx_')->join('wechat_account ON wc_id=wx_ptc_wc_id')->order('wx_ptc_end_time desc')->select();
        foreach ($list as $k=>$v){
            $list[$k]['k']=$k+1;
            if ($v['wx_ptc_status']==1){
                $list[$k]['wx_ptc_status_name']='开启';
            }elseif ($v['wx_ptc_status']==0) {
                $list[$k]['wx_ptc_status_name'] = '关闭';
            }
            $list[$k]['ac']='<button class="layui-btn" onclick="data('.$v['wx_ptc_id'].')" >数据</button>
                <button class="layui-btn" onclick="save('.$v['wx_ptc_id'].')" >修改</button> 
                <button class="layui-btn" onclick="dele('.$v['wx_ptc_id'].')" >删除</button> ';
        }
        $this->ajaxReturn($list,'json');
    }

    //新建海报
    public function addPoster(){
        admin_check();

        if (IS_POST){

            $code = $this->getCode();//生成助力码

            $post=array(
                "wx_ptc_name"            =>  I("wx_ptc_name","","trim"),
                "wx_ptc_start_time"      =>  I("wx_ptc_start_time","","trim"),
                "wx_ptc_end_time"        =>  I("wx_ptc_end_time","","trim"),
                "wx_ptc_status"          =>  I("wx_ptc_status","","trim"),
                "wx_ptc_wc_id"           =>  I("wx_ptc_wc_id","","trim"),
                "wx_create_time"         =>  date("Y-m-d H:i:s"),
                "wx_poster_prompt"       =>  I("wx_poster_prompt","","trim"),
            );

            if ($post['wx_ptc_start_time'] >= $post['wx_ptc_end_time'])
                $this->error('活动结束时间需大于开始时间');

            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =      './Public/Uploads/qr/'; // 设置附件上传目录
            // 上传文件
            $info   =   $upload->upload();

            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功 获取上传文件信息
                $post['wx_ptc_wc_qr'] = 'Uploads/qr/'.$info['wx_ptc_wc_qr']['savepath'].$info['wx_ptc_wc_qr']['savename']; //二维码
                $post['wx_poster_url'] = 'Uploads/qr/'.$info['wx_poster_url']['savepath'].$info['wx_poster_url']['savename']; //海报
            }

            $post['wx_poster_pic'] = 'Uploads/poster/'.$code.'.jpg'; //首张海报


            D()->startTrans(); //开启事务

//            $code = $this->getCode();//生成助力码

            $post['wx_ptc_first_code'] = $code;
            $add_ptc=M('poster_config','wx_')->add($post);

//            $code = $this->getCode();
            $pt_data=array(
                'wx_pt_fans_id' => 0,
                'wx_pt_code'    => $code,
                'wx_pt_wc_id'   => $post['wx_ptc_wc_id'],
                'wx_pt_create_time' => date('Y-m-d H:i:s'),
                'wx_pt_ptc_id'     => $add_ptc
            );
            $add_pt = M('poster','wx_')->add($pt_data);

            createImg($post['wx_poster_url'],$post['wx_ptc_wc_qr'],'nick',$post['wx_ptc_name'],$code);//创建图片


            if ($add_ptc > 0 && $add_pt > 0){
                D()->commit(); //事务提交
                $this->success('活动新建成功');
            }else{
                D()->rollback(); //事务回滚
                $this->error('活动新建失败');
            }

//            $add > 0 ? $this->success('活动新建成功') : $this->error('活动新建失败');
        }else{
            $wx_list = D("WechatAccount")->getWechatAccoun();
            $this->assign('wx_list',$wx_list);
            $this->display();
        }
    }


    //修改活动
    public function save(){
        admin_check();

        $ptc_id = I("wx_ptc_id",0);
        if ($ptc_id <= 0) $this->error("参数有误");

        if (IS_POST){
            $wx_ptc_start_time = I("wx_ptc_start_time","","trim");
            $wx_ptc_end_time = I("wx_ptc_end_time","","trim");
            if (!empty($wx_ptc_start_time))
                $post['wx_ptc_start_time'] = $wx_ptc_start_time;
            if (!empty($wx_ptc_end_time))
                $post['wx_ptc_end_time'] = $wx_ptc_end_time;

            $post['wx_ptc_id'] = $ptc_id;
            $post['wx_ptc_name'] = I("wx_ptc_name","","trim");
            $post['wx_ptc_status'] = I("wx_ptc_status","","trim");
            $post['wx_poster_prompt'] = I("wx_poster_prompt","","trim");

            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            if ($_FILES['wx_ptc_wc_qr']['error']!=4){ //图片不为空才上传
                $upload->rootPath  =      './Public/Uploads/qr/'; // 设置附件上传目录
                // 上传文件
                $info   =   $upload->upload();
                if(!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                }else{// 上传成功 获取上传文件信息
                    foreach($info as $file){
                        $post['wx_ptc_wc_qr'] = 'Uploads/qr/'.$file['savepath'].$file['savename'];
                    }
                }
            }

            if ($_FILES['wx_poster_url']['error']!=4){ //图片不为空才上传
                $upload->rootPath  =      './Public/Uploads/qr/'; // 设置附件上传目录
                // 上传文件
                $info   =   $upload->upload();
                if(!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                }else{// 上传成功 获取上传文件信息
                    foreach($info as $file){
                        $post['wx_poster_url'] = 'Uploads/qr/'.$file['savepath'].$file['savename'];
                    }
                }
            }

            $save=M('poster_config','wx_')->save($post);
            $save != false ? $this->success('活动修改成功') : $this->error('活动修改失败');

        }else{
            $ptc_info = M('poster_config','wx_')->where('wx_ptc_id=%d',$ptc_id)->find(); //根据id获取公众号信息
            $wx_list = D("WechatAccount")->getWechatAccoun();
            $this->assign('wx_list',$wx_list);
            $this->assign('info',$ptc_info);
            $this->display();
        }
    }


    //活动数据页面
    public function data(){
        admin_check();

        $ptc_id = I("wx_ptc_id", 0);
        $info = M('poster_config','wx_')->where('wx_ptc_id=%d',$ptc_id)->join('wechat_account ON wx_ptc_wc_id=wc_id','left')->find();
        $this->assign('info',$info);
        $this->assign('wx_ptc_id',$ptc_id);
        $this->display();
//        echo $ptc_id;exit();
    }

    //获取活动数据
    public function getData(){
        admin_check();

        $ptc_id = I("wx_ptc_id", 0);
        if ($ptc_id > 0){
            $list = M('poster','wx_')->where('wx_pt_ptc_id=%d',$ptc_id)->join('wx_fans ON wx_pt_fans_id=wx_fans_id','left')->order('wx_pt_count desc')->select();
            foreach ($list as $k=>$v){
                $list[$k]['k']=$k+1;
//                if ($v['wx_ptc_status']==1){
//                    $list[$k]['wx_ptc_status_name']='开启';
//                }elseif ($v['wx_ptc_status']==0) {
//                    $list[$k]['wx_ptc_status_name'] = '关闭';
//                }
                $list[$k]['wx_pt_valid_count']=$v['wx_pt_count'] - $v['wx_pt_invalid_count'];
                $list[$k]['ac']='<button class="layui-btn" onclick="details('.$v['wx_pt_id'].')" >详情</button> ';
            }
            $this->ajaxReturn($list,'json');
        }
    }


    //海报活动助力粉丝列表页面
    public function details(){
        admin_check();

        $pt_id = I("wx_pt_id", 0);
//        echo $pt_id;
//        exit();
        $info = M('poster','wx_')->where('wx_pt_id=%d',$pt_id)->join('wx_fans ON wx_fans_id=wx_pt_fans_id','left')->find();
        $this->assign('info',$info);
        $this->assign('pt_id',$pt_id);
        $this->display();
//        echo $ptc_id;exit();
    }

    //获取海报活动助力粉丝列表
    public function getDetails(){
        admin_check();

        $pt_id = I("pt_id", 0);
        if ($pt_id > 0){
            $list = M('poster','wx_')->where('wx_pt_zl_id=%d',$pt_id)
                ->join('wx_fans ON wx_pt_fans_id=wx_fans_id','left')
                ->order('wx_pt_create_time desc')->select();
//            echo M()->_sql();
            foreach ($list as $k=>$v){
                $list[$k]['k']=$k+1;
                if ($v['wx_pt_status']==1){
                    $list[$k]['wx_pt_status_name']='有效';
                }elseif ($v['wx_pt_status']==0) {
                    $list[$k]['wx_pt_status_name'] = '无效';
                }
//                $list[$k]['ac']='<button class="layui-btn" onclick="details('.$v['wx_pt_id'].')" >详情</button> ';
            }
            $this->ajaxReturn($list,'json');
        }
    }



    //删除活动
    public function delete(){
        admin_check();

        $ptc_id = I("wx_ptc_id",0);
        if ($ptc_id <= 0) $this->error("参数有误");

        $delete = M('poster_config','wx_')->where('wx_ptc_id=%d',$ptc_id)->delete();
        $delete ? $this->success("删除成功") : $this->error("删除失败");
    }

    //获取助力码
    public function getCode(){
        $code=rand(100000,999999);
        $pt_code = M('poster','wx_')->where(" wx_pt_code='%s' ",$code )->select();
        if (!empty($pt_code)){
            for ($i=1; $i<=2000; $i++){
                $code=rand(100000,999999);
                $pt_code = M('poster','wx_')->where(" wx_pt_code='%s' ",$code )->find();
                if (empty($pt_code)) break;
            }
        }

        return $code;
    }

}