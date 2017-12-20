<?php
/**
 * 微信Api调用.
 */

namespace Module;

/**
 * Class \Module\Api.
 */
class Api extends \Module\Base
{

    // accessToken缓存key.
    const REDIS_KEY_ACCESS_TOKEN_CACHE = 'weixin_access_token_cache';
    // 调用接口是出现的口令不正确key
    const REDIS_KEY_ACCESS_TOKEN_FAILED = 'weixin_access_token_failed_count';

    /**
     * Get instance of the derived class.
     *
     * @return Api
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
     * 获得帐号访问的accessToken.
     * 
     * @return mixed
     */
    public function getAccessToken()
    {
        $redis = \Module\Redis::Instance()->getRedis();
        $redisKey = self::REDIS_KEY_ACCESS_TOKEN_CACHE . \Config\Common::$weixinInfo['app_id'];
        $token = $redis->get($redisKey);
        if (empty($token)) {
            $this->log(date('Y-m-d H:i:s') . "\t get token failed", 'getTokenByCacheFail');
        }

        return $token;
    }

    /**
     * 获取已关注用户基本信息.
     *
     * @param string $openid 用户openid.
     *
     * @return array
     */
    public function getSubscribeUserInfo($openid)
    {
        $userInfo = array();
        $accessToken = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info";
        $url .= "?access_token={$accessToken}&openid={$openid}&lang=zh_CN";
        $result = $this->request($url, '', 'get');
        if (empty($result['errcode'])) {
            $userInfo = $result;
        }

        return $userInfo;
    }

    /**
     * 创建菜单.
     * 
     * @return boolean
     */
    public function createMenus()
    {
        $configMenus = \Config\Common::$menu;
        if (!empty($configMenus)) {
            $menus = array();
            foreach ($configMenus as $name1 => $menu1) {
                if (empty($menu1['subs'])) {
                    $menu = $this->makeMenuButton($name1, $menu1);
                } else {
                    $menu = array(
                        'name' => $name1,
                        'sub_button' => array()
                    );
                    foreach ($menu1['subs'] as $name2 => $menu2) {
                        $menu['sub_button'][] = $this->makeMenuButton($name2, $menu2);
                    }
                }
                $menus[] = $menu;
            }
            $token = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$token}";
            $params = \Util\Helper::jsonEncdoe(array('button' => $menus));
            $result = \Util\Requset::curlRequest($url, $params, 'post');

            return empty($result['errcode']) ? true : false;
        }

        return false;
    }

    /**
     * 菜单button构建（暂时只支持click/view/miniprogram三种  有其他需求，请自行修改）.
     *
     * @param string $name   菜单名称.
     * @param array  $config 内容配置.
     *
     * @return array
     */
    private function makeMenuButton($name, $config)
    {
        $menu = array(
            'name' => $name,
            'type' => $config['type']
        );
        switch ($config['type']) {
            case 'click':
                $menu['key'] = $config['key'];
                break;
            case 'view':
                $menu['url'] = $config['url'];
                break;
            case 'miniprogram':
                $menu['url'] = $config['url'];
                $menu['appid'] = \Config\Common::$miniprogram['app_id'];
                $menu['pagepath'] = $config['pagepath'];
                break;
            default:
                break;
        }

        return $menu;
    }

    /**
     * 创建二维码ticket.
     *
     * @param string  $actionName 二维码类型.
     * @param array   $sceneInfo  二维码详细信息.
     * @param integer $expire     临时二维码有效期，最大不超过2592000（即30天）.
     *
     * @return array
     */
    public function getQRCode($actionName, $sceneInfo, $expire = 600)
    {
        $data = array(
            'action_name' => $actionName,
            'action_info' => $sceneInfo
        );
        if (in_array($actionName, array('QR_SCENE', 'QR_STR_SCENE'))) {
            $data['expire_seconds'] = $expire;  // 临时二维码需要设置有效期
        }

        $token = $this->getAccessToken();
        $requestUrl = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $token;
        $txt = \Util\Helper::jsonEncdoe($data);
        $res = \Util\Requset::curlRequest($requestUrl, $txt, 'post');

        return $res;
    }

    /**
     * 长链转短链.
     *
     * @param string $url 长链接.
     *
     * @return array
     */
    public function getShortUrl($url)
    {
        $data = array(
            'action' => 'long2short',
            'long_url' => $url
        );
        $token = $this->getAccessToken();
        $requestUrl = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token=" . $token;
        $txt = \Util\Helper::jsonEncdoe($data);
        $res = \Util\Requset::curlRequest($requestUrl, $txt, 'post');

        return $res;
    }

    /**
     * 本接口所上传的图片不占用公众号的素材库中图片数量的5000个的限制。图片仅支持jpg/png格式，大小必须在1MB以下.
     *
     * @param string $file 图片文件地址.
     *
     * @return array
     */
    public function uploadimg($file)
    {
        $data = array(
            'media' => '@' . $file
        );
        $token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=" . $token;
        $res = \Util\Requset::curlRequestt($url, $data, 'post');

        return $res;
    }

}
