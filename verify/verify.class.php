<?php

class verifyCar{
    
    protected $Img=null;
    protected $ImgColor=null;
    protected $FontColor=null;
    protected $verify_arr=null;
    protected $ttf = array('/STZHONGS.TTF'=>1,'/msyhbd.ttf'=>1);
   
    public function createVerify()
    {
            #获取9个字
            $this->initVerify();

            #选出4个字的key 作为验证条件
            $this->title = array_rand($this->verify_arr,4);

            #创建背影画布
            $this->createImg();

            #创建背影画布      
            $this->createImgColor();

            #填充颜色
            $this->imagefill();

            #随机选用字体颜色
            $this->randFontColor();

            #增小点
            $this->creatrImgSundries();

            #生成字
            $verify_key = $this->createFont();
            
            session_start();
            #设置相关验证信息
            $str = '123456789qweasdzxcvbnfghrtyuiojklqwdevsdsdqewsertyuiopdfghjklxcvbnm789456123';
            $md5 = 'sdsdqewseDSuioFFp%%&&dfghjklxcvb))&&945';

            if(isset($_SESSION['verifytoken'])&&!empty($_SESSION['verifytoken'])){
            }else{
                    $verify = str_shuffle($str);
                    $_SESSION['verifytoken'] = $verify;
            }
            $_SESSION['verifyhanzi'] = md5(intval($verify_key).$md5).'huazi';
        
            ob_end_clean();
            header("Content-Type: image/png");
            imagepng($this->Img);
    }
    
    /**
     *  选出9个要验证的字。
     */
    protected function initVerify()
    {
            require 'hanzi.php';
            if(!is_array($hanzi)){
                exit('no');
            }

            shuffle($hanzi);

            $verify_arr = array();
            #选出9个字
            foreach( $hanzi as $k=>$v )
            {
                    $verify_arr[$v['letter']] = $v['word'];
                    if( count($verify_arr) == 9 )
                    {
                            break;
                    }
            }
            $this->verify_arr =  $verify_arr;
    }
    
    
    #创建一个画布
    protected function createImg()
    {
            $this->Img = imagecreatetruecolor(260,300);
    }
    
    
    #创建背影画布 
    protected function createImgColor()
    {
            $this->ImgColor = imagecolorallocate($this->Img,255,255,255);
            imagecolortransparent($this->Img,$this->ImgColor);
    }
    
    
    #填充颜色
    protected function imagefill()
    {
            imagefill($this->Img,0,0,$this->ImgColor);
    }
    
    
    #随机选用字体颜色
    protected function randFontColor()
    {
            $colorArr = array( array(255,0,128), array(241,37,2), array(0,0,255), array(31,196,14),array(0,196,196));
            $color_key = rand(0,4);
            $this->FontColor = imagecolorallocate($this->Img,$colorArr[$color_key][0],$colorArr[$color_key][1],$colorArr[$color_key][2]);
    }
    
    
    #增加杂物
    protected function creatrImgSundries()
    {
            for($i=1;$i<100;$i++)
            {
                    $diancolor = imagecolorallocate($this->Img,rand(10,230),rand(10,230),rand(10,230));
                    imagesetpixel($this->Img,rand(10,180),rand(20,60),$diancolor);
            }
            $linecolor = imagecolorallocate($this->Img,rand(10,230),rand(10,230),rand(10,230));
            imageline($this->Img, 30, rand(30,60), 170, rand(30,60), $linecolor);
    }
        
    
    #生成字
    protected function createFont()
    {
            $i=1;
            $num = rand(0,3);
            foreach( $this->title as $k=>$v )
            {
                    $width = 20*$i;
                    $i = ($i+2);
                    if( $k == $num )
                            imagefttext($this->Img,12,rand(-25,25),$width,rand(45,50),$this->FontColor,VERIFY_PATH_5DCAR.'/STZHONGS.TTF',$v);
                    else
                            imagefttext($this->Img,16,rand(-25,25),$width,rand(45,50),$this->FontColor,VERIFY_PATH_5DCAR.array_rand($this->ttf),$this->verify_arr[$v]);
                    $show[] = $this->verify_arr[$v];
            }

            $i=1;$j=1;$verify_key='';
            foreach( $this->verify_arr as $k=>$v )
            {
                    if( $i<=3 ){
                            $width = (32*$j);
                            $height = 120;
                    } else if($i<=6){
                            $width = (32*$j);
                            $height = 180;
                    } else if($i<=9){
                            $width = (32*$j);
                            $height = 240;
                    }
                    #正确验证的key
                    in_array($v,$show) && $verify_key .=$i;
                    $j=$j+2;$i++;

                    imagefttext($this->Img,16,rand(-20,20),$width,$height,$this->FontColor,VERIFY_PATH_5DCAR.array_rand($this->ttf),$v);

                    $i==4 && $j=1;
                    $i==7 && $j=1;	
            }
            return $verify_key;
    }
    
    
   
}



   
?>

