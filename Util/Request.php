<?php
/**
 * Curl请求.
 */

namespace Util;

/**
 * Class \Util\Requset.
 */
class Requset
{

    /**
     * 发送请求.
     * 
     * @param string  $url         请求地址.
     * @param string  $params      请求参数.
     * @param string  $type        请求类型.
     * @param boolean $errorReport 是否记录报错.
     * 
     * @return string
     */
    public static function curlRequest($url, $params, $type = 'get', $errorReport = true)
    {
        for ($i = 0; $i < 3; ++$i) {
            try {
                $ch = curl_init();
                if ($type == 'get' && !empty($params)) {
                    if (stripos($url, '?') !== false) {
                        $url .= "&{$params}";
                    } else {
                        $url .= "?{$params}";
                    }
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                }
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // 添加超时时间
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // 不验证证书
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    // 不验证证书
                // curl_setopt($ch, CURLOPT_SSLVERSION, 3);
                $result = curl_exec($ch);
                $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                \Module\Base::Instance()->log("{$url}\t{$params}\t{$result}\t{$status}", "requests");
                if (!empty($result) && $status == '200') {
                    $result = json_decode($result, true);
                    if ($errorReport && !empty($result['errcode']) && $result['errcode'] == 40001) {
                        \Module\Base::Instance()->log("{$url}\t" . json_encode($result), 'invalidToken');
                        $redisKey = \Module\Api::REDIS_KEY_ACCESS_TOKEN_FAILED . \Config\Common::$weixinInfo['app_id'];
                        $redis = \Module\Redis::Instance()->getRedis();
                        $redis->incr($redisKey);
                    }
                    break;
                }
            } catch (Exception $e) {
                \Module\Base::Instance()->log($e->getMessage(), "requestsError");
            }
        }

        return $result;
    }

    /**
     * 获取内容.
     * 
     * @param string $url URL.
     * 
     *  @return mixed
     */
    public static function httpGetData($url)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // 不验证证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    // 不验证证书
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            ob_start();
            curl_exec($ch);
            $returnContent = ob_get_contents();
            ob_end_clean();

            $returnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($returnCode != 200) {
                return false;
            }

            return $returnContent;
        } catch (Exception $e) {
            return false;
        }
    }

}
