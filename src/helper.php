<?php
//http请求
if (!function_exists('http')) {
    /**
     * 请求地址
     * @param string $url 请求地址
     * @return string
     */
    function http($url, $data = [], $headers = [], $second = 60){
        return \abc\Curl::getdata($url, $data, $headers, $second);
    }
    
}
//随机数
if (!function_exists('rand_str')) {
    /**
     * 生成随机字符串
     * @param int $len 随机字符串的长度
     * @return string
     */
     function rand_str($len = 8)
    {
        $strArr = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));
        //shuffle($strArr);
        $randStr = '';
        $count = count($strArr);
        for ($i = 0; $i < $len; $i++) {
            $randStr .= $strArr[mt_rand(0, $count - 1)];
        }
        return $randStr;
    }
}
//目录递归返回文件
if (!function_exists('getFiels')) {
    function getFiles($path,&$fiels=[]){
        foreach (glob($path) as $file) {
            is_dir($file) ? getFiles($file . '/*', $fiels):$fiels[] = realpath($file);
        }
    }
}
//下划线转驼峰
if (!function_exists('camelize')) {
    function camelize($uncamelized_words,$separator='_')
    {
    
        $uncamelized_words = $separator. str_replace($separator, " ", strtolower($uncamelized_words));
    
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator );
    
    }
}
//驼峰转——下划线
if (!function_exists('uncamelize')) {
    function uncamelize($camelCaps,$separator='_')
    {
        $camelCaps = lcfirst($camelCaps);
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}
//格式化var_export
if (!function_exists('format_var_export')) {
    function format_var_export($data = [])
    {
        $str = var_export($data, true);
        $str = trim($str);
       $str = preg_replace('/\s+/','',$str);
        return str_replace([
            "array(",
            ",)",
            ],[
             "[",
             "]"
            ],$str);
    //     $string = "<?php\n\nreturn " . var_export($data, TRUE) . ";";
    //   // $string = str_replace("=> \n  array (", "=> [", $string);
    //     //$string = str_replace("),", "],", $string);
    //   // $string = str_replace(");", "];", $string);
    //     $string = str_replace("array (", "[", $string);
    //     $string = str_replace("  ", "    ", $string);
    //     return $string;
    }
}