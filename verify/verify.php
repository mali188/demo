<?php


header("content-type:text/html; charset=utf-8");



define('VERIFY_PATH_5DCAR',dirname(__FILE__));

require 'verify.class.php';

$str = '123456789qweasdzxcvbnfghrtyuiojklqwdevsdsdqewsertyuiopdfghjklxcvbnm789456123';


if( !strpos($_SERVER['HTTP_REFERER'],'5dcar.com') ){
    header("location:/");
    exit;
}


$verify = new verifyCar();

$tag = $_GET['tag'];

$chkey = ($_GET['key']=='aa8855uyt4512');

ob_end_clean();

$callback = $_GET['callback'];
#获取验证码图片
if( $tag == 'picture' && $chkey)
{
    $verify -> createVerify();
   
#获取验证码html内容
} else if( $tag == 'showContent' && $chkey )
{
        echo $callback."(".json_encode(file_get_contents(VERIFY_PATH_5DCAR.'/templets/verify.php')).")";

#获取验证码token
} else if( $tag == 'tokenOnly' && $chkey )
{

        echo $callback."(".json_encode( GetVerifystr() ).")";


#判断验证码 验证是否成功
} else if( $tag == 'authentication' && $chkey )
{
        echo $callback."(".json_encode( PostVerify() ).")";

} else {
    
        header("location:/");
    
}

/**
 * @统一token值，确保和页面的值相同。
 * @return token值。
 */
function GetVerifystr()
{
        session_start();
        if(isset($_SESSION['verifytoken'])&&!empty($_SESSION['verifytoken'])){
                $verify = $_SESSION['verifytoken'];
        }else{
                $verify = str_shuffle( $GLOBALS['str'] );
                $_SESSION['verifytoken'] = $verify;
        }
        
        return array('verify_str'=>$verify,'status'=>1);
}

/**
 * @验证 提交过来的验证码信息 是否正确，
 * 
 * @return 状态码 1为正确 并且返回一串字符串 用来发送验证码时对比是否相同，相同则发送验证码。
 */
function PostVerify()
{         
        session_start();
        if( empty($_GET['verification']) || empty($_GET['verifiytoken']) ){
            header("location:/");
            exit;
        }
        $verify = $_SESSION['verifyhanzi'];
        $token  = $_SESSION['verifytoken'];
        $post_verifiycation = $_GET['verification'].'huazi';
        $post_verifiytoken = $_GET['verifiytoken'];
       
        unset($_SESSION['verifyhanzi']);
        unset($_SESSION['verifytoken']);

        if($post_verifiycation==$verify&&$post_verifiytoken==$token){			
                $_SESSION['myverifytoken'] = str_shuffle( $GLOBALS['str'] );
                return array('status'=>1,'msg'=>$_SESSION['myverifytoken']);	
        }else{
                return array('status'=>0);
        }

}

?>
