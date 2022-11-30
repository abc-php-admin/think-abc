<?php
namespace abc;
use think\Exception;
class Files
{
    //目录软连接只针对addons
    public static function linkAddonsDir($path){
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        $root = app()->getRootPath();
        foreach ($files as $file){
            $realpath = $file->getRealPath();
            $path     = str_replace([$root,"addons/","/static"],["","",""],$realpath);
            $newpath  = $root."public".DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."addons".DIRECTORY_SEPARATOR.$path;
            if ($file->isDir()) {
                $Dir = $newpath;
                !is_dir($Dir) && mkdir($Dir, 0755, true);
            } else {
                $info = pathinfo($newpath);
               !is_dir($info["dirname"]) && mkdir($info["dirname"],0755,true);
                (!is_link($newpath) && is_file($file->getRealPath()))  && (strtoupper(substr(PHP_OS,0,3))==='WIN' ? link($file->getRealPath(),$newpath) :symlink($file->getRealPath(),$newpath));
            }
            
        }
        
    }
    //目录删除
    public static function delDir($path){
        if (!is_dir($path)){return false;}
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file){
            $do  = $file->isDir() ? "rmdir":"unlink";
            $do($file->getRealPath());
        }
        @rmdir($path);
        return true;
    }
    //目录复制
    public static function copyDir($old_path="",$new_path=""){
        if (!is_dir($old_path)){return false;}
        $new_path = $new_path ? $new_path : $old_path."_copy";
        !is_dir($new_path) && mkdir($new_path,0755,true);
        foreach (
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($old_path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            ) as $file
        ) {
            if ($file->isDir()) {
                $Dir = $new_path . DIRECTORY_SEPARATOR . $files->getSubPathName();
                !is_dir($Dir) && mkdir($Dir, 0755, true);
            } else {
                copy($file, $new_path . DIRECTORY_SEPARATOR. $files->getSubPathName());
            }
        }
        return true;
    }
}

        
    