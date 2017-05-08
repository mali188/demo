<?php
header("content-type:text/html; charset=utf-8");
$ob = new se();

$ob->search();

class se{
    
    public $redis_db;
    public $data;
    public $key;
    public $limit;
    
    public function __construct() {
        
        if( empty($_GET['key']) ){
            exit(null);
        }
        if($_GET['limit']){
            $this->limit = intval($_GET['limit']);
        }else{
            $this->limit = 10;
        }
     
        $this->redis_db = new redis();//192.168.1.77
        $this->redis_db->connect( '127.0.0.1','6379' );
        $key =$_GET['key'];
       
        $this->key = strtolower(str_replace(' ','',$key));
    }
    
    public function search()
    {        
        $this->redis_db->select(4);
      
        $list = $this->redis_db->keys( $this->key.'*' );
        if( $list ){
            $this->data = $list;
            $data = $this->searchPbid();
            
            if( $data['list'] ){
                exit( json_encode( $data ) );
            }
            exit( json_encode( $this->searchPserid() ) );
        } else {
            exit( json_encode( $this->searchPserid() ) );
        }
    }
    
    
    public function searchPbid()
    {
            header("content-type:text/html; charset=utf-8");

            $arr = array();

            foreach( $this->data as $v ){
                    $d = $this->redis_db->hGetAll( $v );
                    $arr[ $d['pbid'] ] = $d;

                    if( count($arr)>10 ) break;
            }				
           
            $result = array_values($arr);	
           
            $y_result = $result;

            $array = array('type'=>'pbid','pbid'=>0);
            
            if( count($result) <= 2 ){

                    $this->redis_db->select(6);

                    $pbid = $result[0]['pbid'];
                    $pbid_1 = $result[1]['pbid'];
                    
                    $result = $this->redis_db->get( md5( $pbid ) );
                    if( !$result ){
                        $result = $this->redis_db->get( md5( $pbid_1 ) );
                    }
                    $result = unserialize($result);
                    if( count($result) > $this->limit ){
                            $result = array_splice($result,0,$this->limit);
                    }

                    $array = array('type'=>'pbid','pbid'=>$pbid,'flist'=>$y_result);
            }
            
            $array['list'] = $result;
            return $array;
    }
    
    public function searchPserid()
    {

        $this->redis_db->select(5);

        $list = $this->redis_db->keys( $this->key.'*' );
        
        $result = array();
        foreach( $list as $v ){
            
                $d = $this->redis_db->hGetAll( $v );
                
                if(is_array($d)){
                        $values = array_values($d);
                }
                $result = array_merge($result,$values);
                $result = array_unique($result);
                if( count($result)>10 ) break;
        }
      
        $data = array();
        $this->redis_db->select(6);
        foreach ( $result as $k=>$v ){
            $value = $this->redis_db->hGetAll( $v );
            
            if( $value ){
                $data[] = $value;
            }
        }
        $array = array('type'=>'pserid');
        $data = array_splice($data,0,$this->limit);
        $array['list'] = $data;
        return $array;
    }
}
