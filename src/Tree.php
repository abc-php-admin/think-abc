<?php
namespace abc;
class Tree
{
    //把多个数组合并成一个数组
    public static function arrsToAar($data){
        $arr = [];
        foreach ($data as $key=>$v){
            foreach ($v as $kk=>$v){
                $arr[$kk][$key][]=$v;
            }
        }
        return $arr;
    }
    //数组转成数
    public static function arr2tree($list, $id = 'id', $pid = 'pid', $son = 'sub')
    {
        $tree = $map = array();
        foreach ($list as $item) {
            $map[$item[$id]] = $item;
        }
        foreach ($list as $item) {
            if (isset($item[$pid]) && isset($map[$item[$pid]])) {
                $map[$item[$pid]][$son][] = &$map[$item[$id]];
            } else {
                $tree[] = &$map[$item[$id]];
            }
        }
        unset($map);
        return $tree;
    }
}