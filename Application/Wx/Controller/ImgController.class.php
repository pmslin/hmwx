<?php

namespace Wx\Controller;

use Think\Controller;

class ImgController extends Controller
{
    public function index(){
        /*给图片加文字水印的方法*/
        //路径要加上http://
        $dst_path = 'http://f4.topitme.com/4/15/11/1166351597fe111154l.jpg';
        // $dst_path  = 'http://'.$_SERVER['HTTP_HOST'].'/Public/Uploads/qr/5b0a44e8b91d1.jpg';
        $dst_path  = 'http://'.$_SERVER['HTTP_HOST'].'/Public/Uploads/qr/2018-05-27/5b0a44e8b91d1.jpg';
        $dst = imagecreatefromstring(file_get_contents($dst_path));

        /*imagecreatefromstring()--从字符串中的图像流新建一个图像，返回一个图像标示符，其表达了从给定字符串得来的图像
        图像格式将自动监测，只要php支持jpeg,png,gif,wbmp,gd2.*/

        $font = './t1.ttf';
        $black = imagecolorallocate($dst, 0, 0, 0);
        $red = imagecolorallocate($dst, 241,215, 60);
        //Linux-debian 字体默认路径：/usr/share/fonts/truetype/ttf-dejavu/
        //wx服务器:/usr/share/fonts/lyx/cmr10.ttf
        imagefttext($dst, 40, 10, 50, 60, $black, 'c://WINDOWS//Fonts//simsun.ttc', 'Hello');
        /*imagefttext($img,$size,$angle,$x,$y,$color,$fontfile,$text)
        $img由图像创建函数返回的图像资源
        size要使用的水印的字体大小
        angle（角度）文字的倾斜角度，如果是0度代表文字从左往右，如果是90度代表从上往下
        x,y水印文字的第一个文字的起始位置
        color是水印文字的颜色
        fontfile，你希望使用truetype字体的路径*/
        list($dst_w,$dst_h,$dst_type) = getimagesize($dst_path);
        /*list(mixed $varname[,mixed $......])--把数组中的值赋给一些变量
        像array()一样，这不是真正的函数，而是语言结构，List()用一步操作给一组变量进行赋值*/
        /*getimagesize()能获取到什么信息？
        getimagesize函数会返回图像的所有信息，包括大小，类型等等*/
        switch($dst_type){
            case 1://GIF
                header("content-type:image/gif");
                imagegif($dst);
                break;
            case 2://JPG
                header("content-type:image/jpeg");
                imagejpeg($dst);
                break;
            case 3://PNG
                header("content-type:image/png");
                imagepng($dst);
                break;
            default:
                break;
            /*imagepng--以PNG格式将图像输出到浏览器或文件
            imagepng()将GD图像流(image)以png格式输出到标注输出（通常为浏览器），或者如果用filename给出了文件名则将其输出到文件*/
        }
        imagedestroy($dst);
    }




    //水印
    public function water(){
        $img = file_get_contents($_SERVER['HTTP_HOST'].'Public/Uploads/qr/2018-05-27/5b0a44e8b91d1.jpg',true);
//使用图片头输出浏览器
        echo 123;exit();
        header("Content-Type: image/jpeg;text/html; charset=utf-8");
        echo $img;
        exit;



        $dst_path = 'http://f4.topitme.com/4/15/11/1166351597fe111154l.jpg';
        $src_path = 'http://www.logodashi.com/FileUpLoad/inspiration/636003768803214440.jpg';
//创建图片的实例
        $dst = imagecreatefromstring(file_get_contents($dst_path));
        $src = imagecreatefromstring(file_get_contents($src_path));
//获取水印图片的宽高
        list($src_w, $src_h) = getimagesize($src_path);
//将水印图片复制到目标图片上，最后个参数50是设置透明度，这里实现半透明效果
        imagecopymerge($dst, $src, 10, 10, 0, 0, $src_w, $src_h, 80);

//如果水印图片本身带透明色，则使用imagecopy方法
// imagecopy($dst, $src, 10, 10, 0, 0, $src_w, $src_h);

//输出图片
        list($dst_w, $dst_h, $dst_type) = getimagesize($dst_path);
        switch ($dst_type) {
            case 1://GIF
                header('Content-Type: image/gif');
                imagegif($dst);
                break;
            case 2://JPG
                header('Content-Type: image/jpeg');
                imagejpeg($dst);
                break;
            case 3://PNG
                header('Content-Type: image/png');
                imagepng($dst);
                break;
            default:
                break;
        }
        imagedestroy($dst);
        imagedestroy($src);
    }

}