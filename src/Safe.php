<?php
namespace abc;
class Safe
{
    public static $files = [];
    public static $diffs = [];
    public static $log   = [];
    public static function diff($name="safe.php"){        
        if (is_file($name)){
            $log  = include($name);
            self::getfiles();
            foreach (self::$files as $path=>$md5) {
                if ( !isset($log[$path]) ){
                    self::$diffs[$path] = "新增加文件";
                }else{
                    if ( $md5 != $log[$path] ){
                        self::$diffs[$path] = "文件被更改";
                    }
                    unset($log[$path]);
                }
            }
            foreach ($log as $k=>$v) {
                self::$diffs[$k] = "文件被删除";
            }
            return self::$diffs;
        }else{
            self::getfiles();
            self::save();
            return [];
        }
    }
    public static function save($file="safe.php"){
        !self::$files && self::getfiles();
        return file_put_contents($file, "<?php\treturn " . var_export(self::$files, true) . ";");
    }
    //yield
    public static function getfiles($dir="../"){
        if(is_dir($dir)){
            $file = scandir($dir);
            foreach($file as $f){
                if ($f!='.' && $f!='..') {
                    $path = $dir.'/'.$f;
                    if (is_dir($path)) {
                        self::getfiles($path);
                    } else {
                        self::$files[$path]=md5_file($path);
                    }
                }
            }
        }
        //return self::$files;
    }
    
}