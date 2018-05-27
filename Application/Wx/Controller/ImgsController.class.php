<?php

namespace Wx\Controller;


use Think\Controller;

class ImgsController extends Controller
{
    private $image;
    private $info;

    public function index(){
        $this->setWater('http://f4.topitme.com/4/15/11/1166351597fe111154l.jpg',
            'http://www.logodashi.com/FileUpLoad/inspiration/636003768803214440.jpg',
            '',
            '','3','','img');
    }

    function setWater($imgSrc,$markImg,$markText,$TextColor,$markPos,$fontType,$markType)
    {

        $srcInfo = @getimagesize($imgSrc);
        $srcImg_w  = $srcInfo[0];
        $srcImg_h  = $srcInfo[1];

        switch ($srcInfo[2])
        {
            case 1:
                $srcim =imagecreatefromgif($imgSrc);
                break;
            case 2:
                $srcim =imagecreatefromjpeg($imgSrc);
                break;
            case 3:
                $srcim =imagecreatefrompng($imgSrc);
                break;
            default:
                die("不支持的图片文件类型");
                exit;
        }

        if(!strcmp($markType,"img"))
        {
            if(!file_exists($markImg) || empty($markImg))
            {
                return;
            }

            $markImgInfo = @getimagesize($markImg);
            $markImg_w  = $markImgInfo[0];
            $markImg_h  = $markImgInfo[1];

            if($srcImg_w < $markImg_w || $srcImg_h < $markImg_h)
            {
                return;
            }

            switch ($markImgInfo[2])
            {
                case 1:
                    $markim =imagecreatefromgif($markImg);
                    break;
                case 2:
                    $markim =imagecreatefromjpeg($markImg);
                    break;
                case 3:
                    $markim =imagecreatefrompng($markImg);
                    break;
                default:
                    die("不支持的水印图片文件类型");
                    exit;
            }

            $logow = $markImg_w;
            $logoh = $markImg_h;
        }

        if(!strcmp($markType,"text"))
        {
            $fontSize = 16;
            if(!empty($markText))
            {
                if(!file_exists($fontType))
                {
                    return;
                }
            }
            else {
                return;
            }

            $box = @imagettfbbox($fontSize, 0, $fontType,$markText);
            $logow = max($box[2], $box[4]) - min($box[0], $box[6]);
            $logoh = max($box[1], $box[3]) - min($box[5], $box[7]);
        }

        if($markPos == 0)
        {
            $markPos = rand(1, 9);
        }

        switch($markPos)
        {
            case 1:
                $x = +5;
                $y = +5;
                break;
            case 2:
                $x = ($srcImg_w - $logow) / 2;
                $y = +5;
                break;
            case 3:
                $x = $srcImg_w - $logow - 5;
                $y = +15;
                break;
            case 4:
                $x = +5;
                $y = ($srcImg_h - $logoh) / 2;
                break;
            case 5:
                $x = ($srcImg_w - $logow) / 2;
                $y = ($srcImg_h - $logoh) / 2;
                break;
            case 6:
                $x = $srcImg_w - $logow - 5;
                $y = ($srcImg_h - $logoh) / 2;
                break;
            case 7:
                $x = +5;
                $y = $srcImg_h - $logoh - 5;
                break;
            case 8:
                $x = ($srcImg_w - $logow) / 2;
                $y = $srcImg_h - $logoh - 5;
                break;
            case 9:
                $x = $srcImg_w - $logow - 5;
                $y = $srcImg_h - $logoh -5;
                break;
            default:
                die("此位置不支持");
                exit;
        }

        $dst_img = @imagecreatetruecolor($srcImg_w, $srcImg_h);

        imagecopy ( $dst_img, $srcim, 0, 0, 0, 0, $srcImg_w, $srcImg_h);

        if(!strcmp($markType,"img"))
        {
            imagecopy($dst_img, $markim, $x, $y, 0, 0, $logow, $logoh);
            imagedestroy($markim);
        }

        if(!strcmp($markType,"text"))
        {
            $rgb = explode(',', $TextColor);

            $color = imagecolorallocate($dst_img, $rgb[0], $rgb[1], $rgb[2]);
            imagettftext($dst_img, $fontSize, 0, $x, $y, $color, $fontType,$markText);
        }

        switch ($srcInfo[2])
        {
            case 1:
                imagegif($dst_img, $imgSrc);
                break;
            case 2:
                imagejpeg($dst_img, $imgSrc);
                break;
            case 3:
                imagepng($dst_img, $imgSrc);
                break;
            default:
                die("不支持的水印图片文件类型");
                exit;
        }

        imagedestroy($dst_img);
        imagedestroy($srcim);
    }
}


