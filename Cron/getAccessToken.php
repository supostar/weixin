<?php

/**
 * 维护accessToken的定时任务，每分钟跑一次
 * 
 */
define('ROOT_PATH', __DIR__ . '/../');
require '../Core/SiteEngine.php';
require '../Core/Autoloader.php';
\Core\Autoloader::instance()->addRoot(ROOT_PATH)->init();

$need = false;
$redis = \Module\Redis::Instance()->getRedis();
$failedKey = \Module\Api::REDIS_KEY_ACCESS_TOKEN_FAILED . \Config\Common::$weixinInfo['app_id'];
$failedCount = $redis->get($failedKey);

// token累计不可用超过三次后，强制刷新
if ($failedCount > 3) {
    \Module\Base::Instance()->log($failedCount, "needRefreshToken");
    $redis->del($failedKey);
    $need = true;
} else {
    $tokenKey = \Module\Api::REDIS_KEY_ACCESS_TOKEN_CACHE . \Config\Common::$weixinInfo['app_id'];
    $token = $redis->get($tokenKey);
    $tokenExpire = $redis->ttl($tokenKey);
    // token不存在，只剩10分钟过期（提前刷新token）
    if (empty($token) || $tokenExpire < 600) {
        $need = true;
    }
}

if ($need) {
    $url = "https://api.weixin.qq.com/cgi-bin/token";
    $params = "grant_type=client_credential&appid=" . \Config\Common::$weixinInfo['app_id'] . "&secret=" . \Config\Common::$weixinInfo['app_secret'];
    $result = \Util\Requset::curlRequest($url, $params);
    if (empty($result['access_token'])) {
        \Module\Base::Instance()->log(json_encode($result), "weixinServiceError");   // 微信服务器歇菜了
    } else {
        $token = $result['access_token'];
        $expire = intval($result['expires_in']);
        $redis->setex($redisKey, $expire, $result['access_token']);
        \Module\Base::Instance()->log(json_encode($result), 'getAccessToken');
    }
}
