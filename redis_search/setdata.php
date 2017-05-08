<?php
header("content-type:text/html; charset=utf-8");
require 'hanzi.php';
//require 'mysqli.php';
//exit;
$ob = new inputSearch;
$ob->setBrandParent_SeriseParent();


class inputSearch {
    
    public $redis_db;
    public $mysql_db;
    public $py;
    
    public function __construct()
    {    
        $this->newRedis();
        $this->newMysql();
    }
    public function __destruct() {
        if( $this->mysql_db )
        {
            mysql_close( $this->mysql_db );
        }
    }
    
    private function newRedis()
    {
        $this->py = new py_class();
        $this->redis_db = new redis();
        $this->redis_db->connect( '127.0.0.1','6379' );
        // if (@$_GET['flash_all']) {
            $this->redis_db->select(4);
            $this->redis_db->flushDB();
            $this->redis_db->select(5);
            $this->redis_db->flushDB();
            $this->redis_db->select(10);
            $this->redis_db->flushDB();
        // }
    }
    
    private function newMysql()
    {
        //if(current(explode('.',$_SERVER['HTTP_HOST'])) == 'www3'){//测试环境
            $dbhost = '42.62.25.75';//127.0.0.1
        /*}else{
            $dbhost = '192.168.1.81';
        }*/
        $this->mysql_db = mysql_connect($dbhost,'auto','Xy6BRO1hsVwb.') or die("连接数据库失败");
		//2.选择数据库
		mysql_select_db("auto",$this->mysql_db) or die("选择数据库失败");
		//3.设置字符集
		mysql_query("set names utf8");       
        
    }
    
    public function setAll()
    {
        $this->setBrandParent_SeriseParent();
        
        //$this->setBrand_SeriseParent();
        
        //$this->setSeriseParent();
    }
    
    public function setBrandParent_SeriseParent()
    {   
            // $sql = 'select d.pbid,a.pserid,
            //     concat( d.name,replace(a.name,d.name,"") ) name ,
            //     replace(a.name,d.name,"") jingjian_name ,
            //     d.name pinpai_name ,
            //     a.name yuan_name, b.issale
            //     from gcar_serise_parent a 
            //     LEFT JOIN gcar_serise b on a.serid=b.serid
            //     LEFT JOIN gcar_brand c on b.bid=c.bid
            //     LEFT JOIN gcar_brand_parent d on c.pbid=d.pbid
            //     where b.isopen = 1 AND b.issale = 0';// AND a.pserid=53891';
            
            $sql = 'select d.pbid,a.pserid,
                concat( d.name,replace(a.name,d.name,"") ) name ,
                replace(a.name,d.name,"") jingjian_name ,
                d.name pinpai_name ,
                a.name yuan_name, b.issale
                from gcar_serise_parent a 
                LEFT JOIN gcar_serise b on a.serid=b.serid
                LEFT JOIN gcar_brand c on b.bid=c.bid
                LEFT JOIN gcar_brand_parent d on c.pbid=d.pbid
                where b.isopen = 1 and d.status=1';// AND a.pserid=53891';
            //限制为运营类品牌2017-04-18
            $sql .= " AND d.media_type&2>0";

            //5.执行sql
            $result=mysql_query($sql);
            $data = array();
            
            while( $row = mysql_fetch_assoc($result) )
            {
                $data[] = $row;
        
            }
            //print_r($data) or die();        
            
            /**
             *  处理进口数据
             */
            foreach( $data as $k=>$v )
            {
                if( strpos($v['yuan_name'],'进口') !== false ){
                    $data[$k]['jinkou_jinjian_name'] = '进口'.$v['jingjian_name'];
                    $data[$k]['jinkou_name'] = '进口'.$v['yuan_name'];
                }
            }
            /**
             *  已下是 2组实例
             *  [35] => Array
             *   (
             *       [pbid] => 50343
             *       [pserid] => 52737
             *       [name] => 雪佛兰爱唯欧三厢
             *       [jingjian_name] => 爱唯欧三厢
             *       [pinpai_name] => 雪佛兰
             *       [yuan_name] => 爱唯欧三厢
             *   )
             *  [60] => Array
             *   (
             *       [pbid] => 50331
             *       [pserid] => 51962
             *       [name] => 大众CC(进口)
             *       [jingjian_name] => CC(进口)
             *       [pinpai_name] => 大众
             *       [yuan_name] => 大众CC(进口)
             *       [jinkou_jinjian_name] => 进口CC(进口)
             *       [jinkou__name] => 进口大众CC(进口)
             *   )
             * 
             */
            
            $pbid_data = array();
            $less_arr = array();
            //$this->redis_db->select( 5 );
            set_time_limit( 200 );
            //var_dump($data);exit;
            
            
            foreach( $data as $v ){
                
                $this->redis_db->select(5);
                
                $base64_name = base64_encode( $v['yuan_name'] );
                
                $pname = $this->rePinyin( $v['name'] );
                $jingjian_pname = $this->rePinyin( $v['jingjian_name'] );

                $first_pname = $this->getFirstPinyin( $v['name'] );
                $first_jingjia_pname = $this->getFirstPinyin( $v['jingjian_name'] );
                
                
                $yuan_pname = $this->rePinyin( $v['yuan_name'] );
                $first_yuan_pname = $this->getFirstPinyin( $v['yuan_name'] );
            
                $value = array( 'name'=>$base64_name );
                
                if( isset($v['jinkou_jinjian_name']) ){
                    $jinkou_name = $this->rePinyin( $v['jinkou_jinjian_name'] );
                    $jinkou_quan_name = $this->rePinyin( $v['jinkou_name'] );
                    
                    if($v['issale'] == 0){
                        $this->redis_db->hmset( strtolower( $jinkou_name ),$value );
                        $this->redis_db->hmset( strtolower( $jinkou_quan_name ),$value );
                        $this->redis_db->hmset( strtolower( $v['jinkou_jinjian_name'] ),$value );
                        $this->redis_db->hmset( strtolower( $v['jinkou_name'] ),$value ); 
                    }else{
                        $this->redis_db->del( strtolower( $jinkou_name ) );
                        $this->redis_db->del( strtolower( $jinkou_quan_name ) );
                        $this->redis_db->del( strtolower( $v['jinkou_jinjian_name'] ) );
                        $this->redis_db->del( strtolower( $v['jinkou_name'] ) );
                    }
                }
 
                $less_key = $this->quhanzi(  $v['name']  );
                $cur_less_key = $less_arr[ $less_key ];
                if( ($cur_less_key==null) || !in_array($base64_name, $less_arr[ $less_key ]) ){
                //if( !in_array($base64_name, $less_arr[ $less_key ]) ){
                    $less_arr[ $less_key ][] = $base64_name;
                }

                if($v['issale'] == 0){
                    $this->redis_db->hmset( strtolower( $pname ),$value );
                    $this->redis_db->hmset( strtolower( $jingjian_pname ),$value );
                    $this->redis_db->hmset( strtolower( $first_pname ),$value );
                    $this->redis_db->hmset( strtolower( $first_jingjia_pname ),$value );
                    $this->redis_db->hmset( strtolower( $yuan_pname ),$value );
                    $this->redis_db->hmset( strtolower( $first_yuan_pname ),$value );
                    $this->redis_db->hmset( strtolower( $v['name'] ),$value ); 
                    $this->redis_db->hmset( strtolower( $v['jingjian_name'] ),$value ); 
                }else{
                    $this->redis_db->del( strtolower( $pname ) );
                    $this->redis_db->del( strtolower( $jingjian_pname ) );
                    $this->redis_db->del( strtolower( $first_pname ) );
                    $this->redis_db->del( strtolower( $first_jingjia_pname ) );
                    $this->redis_db->del( strtolower( $yuan_pname ) );
                    $this->redis_db->del( strtolower( $first_yuan_pname ) );
                    $this->redis_db->del( strtolower( $v['name'] ) );
                    $this->redis_db->del( strtolower( $v['jingjian_name'] ) );
                }
                
                //获取价格
                $sql = 'select min(price) min_price,max(price) max_price from gcar_price_manu where pserid='.$v['pserid'];
                $result = mysql_query($sql);
                $price  = mysql_fetch_assoc($result);
                
                $this->redis_db->select(6);
                if($v['issale'] == 0){
                    $this->redis_db->hmset( $base64_name,array('name'=>$v['yuan_name'],'pbid'=>$v['pbid'],'pserid'=>$v['pserid'],'min_price'=> $price['min_price'] ,'max_price'=> $price['max_price']) );                 
                    $pbid_data[$v['pbid']][] = array('name'=>$v['name'],'pserid'=>$v['pserid'],'pbid'=>$v['pbid'],'min_price'=> $price['min_price'] ,'max_price'=> $price['max_price']);
                }else{
                    $this->redis_db->del( $base64_name );
                }

                unset( $pname,$jingjian_pname,$first_pname,$first_jingjia_pname,$yuan_pname,$first_yuan_pname,$base64_name,$less_key );
            }
            //exit;
            $this->redis_db->select(5);
            foreach( $less_arr as $k=>$v ){
                $this->redis_db->hmset( strtolower( $k ),$v );
            }

            $this->redis_db->select(6);
            foreach( $pbid_data as $k=>$v )
            {
                $this->redis_db->set( md5($k),  serialize($v) );
            }
            
            $data = array();
            $sql = 'select * from gcar_brand_parent';

            $result = mysql_query($sql);
            while( $row = mysql_fetch_assoc($result) )
            {
                $data[] = $row;
            }
            
            //品牌
            $this->redis_db->select( 4 );

            foreach( $data as $k=>$v ){
                $arr = array('name'=>$v['name'],'pbid'=>$v['pbid']);
                if($v['status'] == 1){
                    $this->redis_db->hmset( strtolower($v['ename']),$arr );

                    $this->redis_db->hmset( strtolower( $this->rePinyin( $v['name'] ) ),$arr );

                    $this->redis_db->hmset( strtolower( $this->getFirstPinyin( $v['name'] ) ),$arr );
                    
                    $this->redis_db->hmset( strtolower($v['name']),$arr );
                }else{
                    $this->redis_db->del( strtolower($v['ename']),$arr );

                    $this->redis_db->del( strtolower( $this->rePinyin( $v['name'] ) ),$arr );

                    $this->redis_db->del( strtolower( $this->getFirstPinyin( $v['name'] ) ),$arr );
                    
                    $this->redis_db->del( strtolower($v['name']),$arr );
                }
            }        
        
    }
    
    public function setBrand_SeriseParent()
    {
        
    }
    
    public function setSeriseParent()
    {
        
    }
    
    /**
     *　获取汉字首字母拼音
     */
    protected function getFirstPinyin( $name ){
            preg_match_all( '/[\x{4e00}-\x{9fa5}]/ui' ,$name ,$arr );
            if( $arr[0] ){
                    foreach( $arr[0] as $v){
                            $pinyin = $this->rePinyin( $v );
                            $name = str_replace( $v, $pinyin[0], $name);
                    }
            }
            return $name;
    }
    
    /**
     *　去除名字中的汉字
     */
    protected function quhanzi( $name ){
        
            preg_match( '/[a-z0-9]+/i' ,$name ,$arr );
            return  $arr[0] ;
    }
    
    /**
     *　获取汉字转换拼音
     */
    protected function rePinyin( $name ){

            preg_match_all( '/([\x{4e00}-\x{9fa5}]|\w)+/iu' ,$name ,$arr );

            $str = '';
            for($i=0,$length=count($arr[0]);$i<$length;$i++ ){
                    $str .= $arr[0][$i];
            }
          

            return $this->py -> str2py( $str,1 );
    }
}

echo '完成';
