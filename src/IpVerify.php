<?php
namespace abc;
class IpVerify
{
    //默认配置
    protected $_config = [];
    public function __construct($arr=array()) {
        if (config('ip.')) {
            $this->_config = array_merge($this->_config, config('ip.'));
        }
        if ($arr){
            $this->_config = array_merge($this->_config, $arr);
        }       
        
    }
    //检查ip是否被允许 
    public static function check_ip($ips="",$ip=""){
        if (!is_array($ips)){
            $ips=explode(",",$ips);
        }
       $ALLOWED_IP=$ips; //ips是数组形式
       $IP=$ip ? $ip:request()->ip();
       $on=true;
        $check_ip_arr= explode('.',$IP);//要检测的ip拆分成数组  
        if(!in_array($IP,$ALLOWED_IP)) { //判断访问的ip有没有在限制的ip数组中
            foreach ($ALLOWED_IP as $val){
               if(strpos($val,'*')!==false){//发现有*号替代符 
                    $arr=array();//  
                     $arr=explode('.', $val); 
                     $on=true;//用于记录循环检测中是否有匹配成功的  
                     for($i=0;$i<4;$i++){ 
                       if($arr[$i]!='*'){//不等于*  就要进来检测，如果为*符号替代符就不检查  
                            if($arr[$i]!=$check_ip_arr[$i]){ 
                                $on=false; 
                                break;//终止检查本个ip段 继续检查下一个ip 段 
                            } 
                        }else{break;}//存在就没有必要在此执行for循环了
                    } //end for
                    if($on){break;} //如果是true则找到有一个匹配成功的就返回   跳出foreach
                     
               }else{$on=false;}//对ip末尾不是通配符进行检测
               
               
            }//end foreach  
             
        }//end if
        return $on;
    }
}