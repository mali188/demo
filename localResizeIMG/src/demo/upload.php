<?php
	$stream=$_POST['img'];
	echo $stream;die;
	if (preg_match('/(?<=\/)[^\/]+(?=\;)/',$stream,$pregR)) $streamFileType ='.' .'jpg'; 
	print_r($pregR);die;
	//读取扩展名，如果你的程序仅限于画板上来的，那一定是png，这句可以直接streamFileType 赋值png
	$streamFileRand = date('YmdHis').rand(1000,9999);    
	//产生一个随机文件名（因为你base64上来肯定没有文件名，这里你可以自己设置一个也行）
	$upPath="./";
	$streamFilename = $upPath.$streamFileRand .$streamFileType;
	//处理base64文本，用正则把第一个base64,之前的部分砍掉
	preg_match('/(?<=base64,)[\S|\s]+/',$stream,$streamForW);
	if (file_put_contents($streamFilename,base64_decode($streamForW[0]))===false){
	   echo "0";
	   exit;
	}else{
		
	   echo "1";
	   exit;
	}
?>