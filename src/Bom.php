<?php  
namespace abc;
class Bom
{
    public static $basedir="../";
    public static $auto = 1;
    public static $files =[];
    public static function check($path="")
    {
        $path && self::$basedir = $path;
        self::checkdir(self::$basedir);
        return self::$files;
    }
    public static function checkdir($basedir)
    {
        if ($dh = opendir($basedir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    if (! is_dir($basedir . "/" . $file)) {
                        echo self::checkBOM("$basedir/$file");
                        //echo "filename: $basedir/$file <br>";
                    } else {
                        $dirname = $basedir . "/" .$file;
                        self::checkdir($dirname);
                    }
                }
            }
            closedir($dh);
        }
       
    }
    public static function checkBOM($filename)
    {
        if (!is_file($filename)){return ;}
        $contents    = file_get_contents($filename,FALSE,NULL,0,10);//不必要读取全部    
        $charset [1] = substr($contents, 0, 1);
        $charset [2] = substr($contents, 1, 1);
        $charset [3] = substr($contents, 2, 1);
        $info        = "BOM Not Found.";
        $bom         = 0;
        if (ord($charset [1]) == 239 && ord($charset [2]) == 187 && ord($charset [3]) == 191) {
            if (self::$auto == 1) {
                $contents = file_get_contents($filename); 
                $rest     = substr($contents, 3);
                self::rewrite($filename, $rest);
                $info     = "BOM found, automatically removed.";
            } else {
                $info     = "BOM found.";
            }
            $bom = 1;
        } else {
            
        }
        self::$files[$filename] = [
            "bom"         => $bom,  
            "info"        => $info,  
            "path"        => $filename,
            //"size"        => size(sprintf("%u", filesize($filename))),
            //"create_time" => date("Y-m-d H:i:s",filectime($filename)),
            //"update_time" => date("Y-m-d H:i:s",filemtime($filename)),
        ];
        
        
    }
    public static function rewrite($filename, $data)
    {
        try{
            $filenum = fopen($filename, "w");
            flock($filenum, LOCK_EX);
            fwrite($filenum, $data);
            fclose($filenum);
        }catch (Exception $e) {}
        
    }
}




