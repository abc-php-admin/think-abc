<?php
namespace abc;
use think\facade\Db;
class Auth
{
    //检测是否有权限
    public static function check($node,$uid=0){
        $uid = $uid ?: session("admin.id");
        //list($module, $controller, $action) = explode('/', str_replace(['?', '=', '&'], '/', $node . '///'));
        //$auth_node = strtolower(trim("{$module}/{$controller}/{$action}", '/'));
        //$auth_node = strtolower(trim("{$controller}/{$action}", '/'));
        if (in_array($node, self::getUserNode())) {
            return true;
        }
        return false;
    }
    //根据登陆用户的权限分组获取登陆节点信息
    public static function getUserNode($group_ids=""){
        $group_ids = $group_ids ?: session("admin.group_ids");
        $nodes = [];
        if ($group_ids){
            $map = [
                ["status", "=", 1],
                ["id", "in", $group_ids]
            ];
            $db = Db::name("auth_group")->where($map)->column("rules");
            $rule = [];
            if ($db) {
                foreach ($db as $v){
                  $rule = array_merge($rule,explode(',', $v));
                }
            }
            $where = [ ["id", "in", $rule]];
            $note = Db::name("auth_node")->where($where)->field('node')->order("sort desc")->select()->toArray();
            foreach ($note as $k=>$v) {
                $nodes[] = strtolower($v["node"]);
            }
        }
        return $nodes;
    }
    
}