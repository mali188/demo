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
