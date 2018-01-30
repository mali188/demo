<?php

/**
 * [get_hash_table mysql 分表算法   根据用户ID进行分表]
 * @Author   malixiao
 * @DateTime 2016-08-29T13:22:22+0800
 * @param    [type]                   $table  [表名]
 * @param    [type]                   $userid [用户ID]
 * @return   [type]                           [description]
 */
function get_hash_table($table,$userid) {  

	$str = crc32($userid);  
	if($str<0){  
		$hash = "0".substr(abs($str), 0, 1);  
	}else{  
		$hash = substr($str, 0, 2);  
	}  
	return $table."_".$hash;  

}
  
echo get_hash_table('message','1');     //结果为message_10  
echo get_hash_table('message','2');    //结果为message_13  



//php文件hash算法，秒传原理
header('Content-type:text/html;Charset=UTF-8');
 
define('blockSize', 4*1024*1024);
 
var_dump(fileHash('test.wmv'));
var_dump(fileHash('asdf.wmv'));
 
function fileHash($file)
{
    $f = fopen($file, "r");
    if (!$f) exit("open $file error");
 
    $fileSize = filesize($file);
    $buffer   = '';
    $sha      = '';
    // 一共有多少分片
    $blkcnt   = $fileSize/blockSize;
    if ($fileSize % blockSize) $blkcnt += 1;
    // 把数据装入一个二进制字符串
    $buffer .= pack("L", $blkcnt);
    if ($fileSize <= blockSize) {
        $content = fread($f, blockSize);
        if (!$content) {
            fclose($f);
            exit("read file error");
        }
        $sha .= sha1($content, TRUE);
    } else {
        for($i=0; $i<$blkcnt; $i+=1) {
            $content = fread($f, blockSize);
            if (!$content) {
                if (feof($f)) break;
                fclose($f);
                exit("read file error");
            }
            $sha .= sha1($content, TRUE);
        }
        $sha = sha1($sha, TRUE);
    }
    $buffer .= $sha;
    $hash = urlSafeEncode(base64_encode($buffer));
    fclose($f);
    return array($hash, null);
}
 
function urlSafeEncode($data)
{
    $find = array('+', '/');
    $replace = array('-', '_');
    return str_replace($find, $replace, $data);
}
