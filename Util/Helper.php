<?php
/**
 * 工具类.
 */

namespace Util;

/**
 * Class \Util\Helper.
 */
class Helper
{

    /**
     * 生成随机字符串.
     * 
     * @param integer $length 长度.
     *
     * @return string
     */
    public static function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 将数据转换为json格式.
     *
     * @param mixed $data 数据.
     *
     * @return string
     */
    public static function jsonEncdoe($data)
    {
        if (PHP_VERSION >= '5.4.0') {
            $string = json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            self::arrayRecursive($data, 'urlencode', true);
            $json = json_encode($data);
            $string = urldecode($json);
        }

        return $string;
    }

    /**
     * 使用特定function对数组中所有元素做处理.
     *
     * @param string  &$array             要处理的字符串.
     * @param string  $function           要执行的函数.
     * @param boolean $apply_to_keys_also 处理键.
     *
     * @return mixed
     */
    private static function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            return false;
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }

            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }

        $recursive_counter--;
    }

}
