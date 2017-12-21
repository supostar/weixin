<?php
/**
 * 微信JsApi相关.
 */

namespace Module;

/**
 * Class \Module\JsApi.
 */
class JsApi extends \Module\Base
{

    // jsapi_ticket缓存key前缀.
    const JSAPI_TICKET_PREFIX = "jsapi_ticket_";

    /**
     * Get instance of the derived class.
     *
     * @return JsApi
     */
    public static function Instance()
    {
        $className = get_called_class();
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new $className;
        }
        return self::$instances[$className];
    }

    /**
     * 获取微信jssdk的配置.
     *
     * @param string $url 当前网页url.
     * 
     * @return array
     */
    public function getJsSdkConfig($url)
    {
        if (empty($url)) {
            return false;
        }

        $weixinInfo = \Config\Common::$weixinInfo;
        $jsapiTicket = $this->getJsapiTicket($weixinInfo['app_id'], $weixinInfo['app_secret']);
        if ($jsapiTicket) {
            $timestamp = time();
            $nonceStr = \Util\Helper::createNonceStr();

            $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
            $signature = sha1($string);

            $signPackage = array(
                "appId" => $weixinInfo['app_id'],
                "timestamp" => $timestamp,
                "nonceStr" => $nonceStr,
                "signature" => $signature,
            );

            return $signPackage;
        } else {
            return false;
        }
    }

    /**
     * 获取jsapi_ticket.
     *
     * @param string $appId Appid.
     *
     * @return mixed
     */
    public function getJsapiTicket($appId)
    {
        $redisKey = self::JSAPI_TICKET_PREFIX . $appId;
        $ticket = \Module\Redis::Instance()->getRedis()->get($redisKey);
        if ($ticket) {
            return $ticket;
        }

        return $this->refreshJsapiTicket($appId);
    }

    /**
     * 调用微信接口刷新jsapi_ticket.
     *
     * @param string $appId Appid.
     *
     * @return mixed
     */
    private function refreshJsapiTicket($appId)
    {
        $accessToken = \Module\Api::Instance()->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=" . $accessToken;
        $json = \Util\Requset::curlRequest($url);
        $this->log($json, "refreshJsapiTicket");

        $result = !empty($json) ? json_decode($json, true) : array();
        if (isset($result['errcode']) && $result['errcode'] == 0 && !empty($result['ticket'])) {
            $ticket = $result['ticket'];
            $expire = intval($result['expires_in']) - 600;
            $redisKey = self::JSAPI_TICKET_PREFIX . $appId;
            \Module\Redis::Instance()->getRedis()->setex($redisKey, $expire, $ticket);

            return $ticket;
        }
        $this->log($json, "refreshJsapiTicketFail");

        return false;
    }

}
