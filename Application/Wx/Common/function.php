<?php


/**
 * 超管
 *  账号管理员权限检测，roleid=1和2的可以修改，1超管 2账号管理员
 */
function admin_check()
{
    if (!in_array(session('roleid'), array(1, 2))) {
//         error('没有操作权限');
        echo '没有操作权限';
        exit();
    }
}


//获取助力码
function getCode(){
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

//添加文字和图片水印 $dst_path海报图片  $src_path二维码小图  $head_path头像图片  $username粉丝名字  $ptc_name活动名称  $code以助力码命名文件名称
function createImg($dst_path='' , $head_path='' ,$username='' ,$ptc_name ,$code){
    /*给图片加文字水印的方法*/
    //路径要加上http://
//        $dst_path = 'http://f4.topitme.com/4/15/11/1166351597fe111154l.jpg'; //网络图片
    // $dst_path  = 'http://'.$_SERVER['HTTP_HOST'].'/Public/Uploads/qr/5b0a44e8b91d1.jpg';
    $dst_path  = 'http://'.$_SERVER['HTTP_HOST'].'/Public/'.$dst_path; //海报图片

//    $src_path  = 'http://'.$_SERVER['HTTP_HOST'].'/Public/'.$src_path; //二维码小图

    $head_path  = 'http://'.$_SERVER['HTTP_HOST'].'/Public/'.$head_path; //头像图片
//        $src_path1 = 'http://www.logodashi.com/FileUpLoad/inspiration/636003768803214440.jpg'; //小图
    $dst = imagecreatefromstring(file_get_contents($dst_path));
//    $src = imagecreatefromstring(file_get_contents($src_path));
    $head = imagecreatefromstring(file_get_contents($head_path));

//        $src1 = imagecreatefromstring(file_get_contents($src_path1));

    /*imagecreatefromstring()--从字符串中的图像流新建一个图像，返回一个图像标示符，其表达了从给定字符串得来的图像
    图像格式将自动监测，只要php支持jpeg,png,gif,wbmp,gd2.*/

    //Linux-debian 字体默认路径：/usr/share/fonts/truetype/ttf-dejavu/
//    $font = 'c://WINDOWS//Fonts//simsun.ttc'; //windows本地
    $font = '/usr/share/fonts/lyx/simsun.ttc'; //wx服务器 Linux
    $black = imagecolorallocate($dst, 0, 0, 0);
    //wx服务器:/usr/share/fonts/lyx/cmr10.ttf 不支持中文
    imagefttext($dst, 25, 0, 80, 950, $black, $font, $username.'正在参加：');
    imagefttext($dst, 25, 0, 80, 1000, $black, $font, '《'.$ptc_name.'》');

    imagefttext($dst, 25, 0, 20, 1400, $black, $font, '扫码关注，回复'.$code.'帮好友助力！');
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



    //添加二维码图片水印
    //获取水印图片的宽高
//    list($src_w, $src_h) = getimagesize($src_path);
//    //将水印图片复制到目标图片上，最后个参数50是设置透明度，这里实现半透明效果
//    imagecopymerge($dst, $src, 560, 1200, 0, 0, $src_w, $src_h, 100);



    //头像图片
    list($head_w, $head_h) = getimagesize($head_path);
    imagecopymerge($dst, $head, 10, 930, 0, 0, $head_w, $head_h, 100);

//        imagecopymerge($dst, $src1, 60, 100, 0, 0, $src_w, $src_h, 100);
//echo $dst_type;exit();

    switch($dst_type){
        case 1://GIF
            header("content-type:image/gif");
            imagegif($dst,'./Public/Uploads/poster/'.$code.'.jpg');
            break;
        case 2://JPG
            header("content-type:image/jpeg");
            imagejpeg($dst,'./Public/Uploads/poster/'.$code.'.jpg');
            break;
        case 3://PNG
            header("content-type:image/png");
            imagepng($dst,'./Public/Uploads/poster/'.$code.'.jpg');
            break;
        default:
            break;
        /*imagepng--以PNG格式将图像输出到浏览器或文件
        imagepng()将GD图像流(image)以png格式输出到标注输出（通常为浏览器），或者如果用filename给出了文件名则将其输出到文件*/
    }


    imagedestroy($dst);
//    imagedestroy($src);
    imagedestroy($head);
}


//php实现下载远程图片保存到本地的方法
function downloadImage($url, $path='images/',$filename)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    $file = curl_exec($ch);
    curl_close($ch);

    saveAsImage($url, $file, $path,$filename);
}
function saveAsImage($url, $file, $path,$filename)
{
//        $filename = pathinfo($url, PATHINFO_BASENAME);
    $resource = fopen($path . $filename, 'a');
    fwrite($resource, $file);
    fclose($resource);
}





/**
 * Excel导出
 * @param data 导出数据
 * @param title 表格的字段名 字段长度有限制，一般都够用，可以改变 $length1和$length2来增长
 * @return subject 表格主题 命名为主题+导出日期
 * @ary->merge array 合并 例:A1:D1                            A1到D1合并
 * @ary->height array OR string 高度 例:1-10                            第一行高度为10 如果为一个数字，即为默认高度
 * @ary->width array 宽度 例:A-10                      A行宽度为10
 * @ary->size array OR string 字体大小 例:A1-10                  第一行字体为10 如果为一个数字，即为默认大小
 * @ary->bold array 加粗 例:A1:D1                             A1到D1加粗
 * @ary->border array string 边框颜色 例:A1:D1-#CC0000    A1-D1改变边框颜色（注：不能为英文）
 * @ary->background array 背景颜色  例:A1:D1-#CC0000    A1-D1改背景颜色（注：不能为英文）
 * ***********例子**************
 * $merge[0] = "A1:D1";  //A1到D1合并
 * $bold[0] = "A2:D2";  //A2到D2字体加粗
 * $height[0] = "5-50"; //第五行的高度为50
 * $width[0] = "A-50";  //A列的宽度为50
 * $border[0] = "A2:D2-#CC0000";  //A2到D2的边框为#CC0000(红色)
 * $size[0] = "A2:D2-15";  //A2到D2的字体为15
 * $background[0] = "A2:D2-#CC0000"; //A2到D2的背景为#CC0000(红色)
 * $ayr = array(
 * 'merge' => $merge,
 * 'bold' => $bold,
 * 'height' => $height,
 * 'width' => $width,
 * 'border' => $border,
 * 'background' => $background,
 * 'size' => $size
 * );
 */
function exportExcel($data, $title, $subject, $ary = null)
{
    Vendor('PHPExcel.PHPExcel');

    Vendor('PHPExcel.PHPExcel.IOFactory');
    Vendor('PHPExcel.PHPExcel.Reader.Excel5');
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    set_time_limit(0);
    @ini_set('memory_limit', '256M');
    // Set properties
    $objPHPExcel->getProperties()->setCreator("ctos")
        ->setLastModifiedBy("ctos")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");
    $length1 = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH');
    $length2 = array('A1', 'B1', 'C1', 'D1', 'E1', 'F1', 'G1', 'H1', 'I1', 'J1', 'K1', 'L1', 'M1', 'N1', 'O1', 'P1', 'Q1', 'R1', 'S1', 'T1', 'U1', 'V1', 'W1', 'X1', 'Y1', 'Z1', 'AA1', 'AB1', 'AC1', 'AD1', 'AE1', 'AF1', 'AG1', 'AH1');
    //set width
    for ($a = 0; $a < count($title); $a++) {
        $objPHPExcel->getActiveSheet()->getColumnDimension($length1[$a])->setWidth(20);
    }
    //set font size bold
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle($length2[0] . ':' . $length2[count($title) - 1])->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle($length2[0] . ':' . $length2[count($title) - 1])->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle($length2[0] . ':' . $length2[count($title) - 1])->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // set table header content
    for ($a = 0; $a < count($title); $a++) {
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($length2[$a], $title[$a]);
    }
    for ($i = 0; $i < count($data); $i++) {
        $buffer = $data[$i];
        $number = 0;
        foreach ($buffer as $value) {
            $objPHPExcel->getActiveSheet(0)->setCellValueExplicit($length1[$number] . ($i + 2), $value, PHPExcel_Cell_DataType::TYPE_STRING); //设置单元格为文本格式
            $number++;
        }
        unset($value);
        $objPHPExcel->getActiveSheet()->getStyle($length1[0] . ($i + 2) . ':' . $length1[$number - 1] . ($i + 2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($length1[0] . ($i + 2) . ':' . $length1[$number - 1] . ($i + 2))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getRowDimension($i + 2)->setRowHeight(16);
    }
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    /*     * *******************************样式********************************** */
    //单元格合并
    for ($i = 0; $i < count($ary['merge']); $i++) {
        $objPHPExcel->getActiveSheet()->mergeCells($ary['merge'][$i]);
        $objPHPExcel->getActiveSheet()->getStyle($ary['merge'][$i])->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($ary['merge'][$i])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }
    // 设置宽度
    for ($i = 0; $i < count($ary['width']); $i++) {
        $w = split("-", $ary['width'][$i]);
        $objPHPExcel->getActiveSheet()->getColumnDimension($w[0])->setWidth($w[1]);
    }
    //设置行高度
    if (is_array($ary['height'])) {
        for ($i = 0; $i < count($ary['height']); $i++) {
            $h = split("-", $ary['height'][$i]);
            $objPHPExcel->getActiveSheet()->getRowDimension($h[0])->setRowHeight($h[1]); //第一行行高
        }
    } else {
        if ($ary['height'] > 0) {
            $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight($ary['height']); //设置默认行高
        }
    }
    //字体和样式
    if (is_array($ary['size'])) {
        for ($i = 0; $i < count($ary['size']); $i++) {
            $s = split("-", $ary['size'][$i]);
            $objPHPExcel->getActiveSheet()->getStyle($s[0])->getFont()->setSize($s[1]); //字体大小
        }
    } else {
        if ($ary['size'] > 0) {
            //默认字体大小
            $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize($ary['size']);
        }
    }
    //加粗
    for ($i = 0; $i < count($ary['bold']); $i++) {
        $objPHPExcel->getActiveSheet()->getStyle($ary['bold'][$i])->getFont()->setBold(true); //第二行是否加粗
    }
    //边框样式
    for ($i = 0; $i < count($ary['border']); $i++) {
        $b = split("-", $ary['border'][$i]);
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN, //设置border样式
                    'color' => array('argb' => $b[1]),
                ),
            ),
        );

        $objPHPExcel->getActiveSheet()->getStyle($b[0])->applyFromArray($styleArray);
    }
    //背景颜色
    for ($i = 0; $i < count($ary['background']); $i++) {
        $b = split("-", $ary['background'][$i]);
        $objPHPExcel->getActiveSheet()->getStyle($b[0])->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($b[0])->getFill()->getStartColor()->setARGB($b[1]);
    }
    //全部居中  非合并单元格
    $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    /*     * ***************************************************************** */
    ob_end_clean(); //清除缓冲区,避免乱码
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=' . $subject . '(' . date('Y-m-d') . ')生成.xls');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
}

