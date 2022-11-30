<?php
namespace abc;
use think\facade\Db;
use think\Exception;
use PhpZip\ZipFile;
use PhpZip\Exception\ZipException;
class Addon
{
     /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @access public
     * @param  string  $name 字符串
     * @param  integer $type 转换类型
     * @param  bool    $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public static function parseName($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);
            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }

        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
    //表单配置文件获取
    public static function getConfig($name="",$type="json"){
        $file  = self::getAddonDir($name)."/config.{$type}";
        if (is_file($file)){
            
            $type=="json" ? $data = file_get_contents($file) : $data= json_encode(include($file));
            return $data;//json_decode($data,true);
        }
        return "";
    }
    //表单配置文件生成
    public static function setConfig($name="",string $data,$type="json"){
        $file  = self::getAddonDir($name)."/config.{$type}";
        file_put_contents($file,$data);
    }
    /**
     * 获取指定插件的目录
     */
    public static function getAddonDir($name="")
    {
        return app()->getRootPath()."addons/{$name}";
    }
    //修改插件列表的志
    public static function getAddonIni(string $name,array $data =[]){
        $file = self::getAddonDir($name)."/info.ini";
        $arr  = parse_ini_file($file, true, INI_SCANNER_TYPED);
        $arr  = array_merge($arr,$data);
        $str  = "";
        foreach ($arr as $k=>$v){
            $str .= "{$k}={$v}\n";
        }
        file_put_contents($file,$str);
    }
    //获取插件列表
    public static function getAddonList(){
        $path = app()->getRootPath()."addons/";
        $arr  = [];
        foreach (glob($path. '*/*.ini') as $file){
            $rows = parse_ini_file($file, true, INI_SCANNER_TYPED);
            $rows["is_install"]= true;
            $arr[] = $rows;
             // $config =  parse_ini_file($file, true, INI_SCANNER_TYPED);
            // if (isset($config["name"])){
            //     $arr[$config["name"]] = $config;
            // }
        }
        return $arr;
    }
    //获取init参数
    public static function getinfo(string $content=""){
        if (!$content){
            throw new Exception("配置参数异常");
        }
        if (!is_file($content)){
            $path  = "/tmp/info.init";
            file_put_contents($path,$content);
        }else{
            $path  = $content;
        }
        
        return parse_ini_file($path, true, INI_SCANNER_TYPED) ?: [];
        
    }
    //检查目录文件是否合规
    public static function checkfiles(array $paths){
        $fiels  = ["info.ini","config.php","Plugin.php"];
        $result = true;
        foreach ($fiels as $v){
            if (!in_array($v,$paths)){
                $result = false;
                break;
            }
        }
        return $result;
    }
    //数据库安装
    public static function install_table($dir){
        $file = $dir."/install.sql";
        if (is_file($file)){
            $lines = file($file);
            $templine = '';
            foreach ($lines as $line) {
                if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*') {
                    continue;
                }

                $templine .= $line;
                if (substr(trim($line), -1, 1) == ';') {
                    $templine = str_ireplace('__PREFIX__', config('database.connections.mysql.prefix'), $templine);
                    $templine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $templine);
                    try {
                        Db::execute($templine);
                    } catch (\PDOException $e) {
                        //$e->getMessage();
                    }
                    $templine = '';
                }
            }
        }
    }
    //解压文件
    public static function unzip($path){
        $zip = new ZipFile();
        $zip->openFile($path);
        $listFiles = $zip->getListFiles();
        if (!self::checkfiles($listFiles)){
            throw new Exception("addons is error");
        }
        $info = self::getinfo($zip->getEntryContents("info.ini"));
        if (!isset($info["name"])){
            throw new Exception("配置文件错误");
        }
        $dir = self::getAddonDir($info["name"]);
        // 如果存在目录 备份 如果有异常恢复之前
        !is_dir($dir) && @mkdir($dir, 0755, true);
        //直接覆盖
        $zip->extractTo($dir);
        //安装数据库
        self::install_table($dir);
        //安装菜单
        $zip->close();
        return $dir;
    }
}