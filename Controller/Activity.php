<?php
/**
 * 活动相关.
 */

/**
 * Controller_Activity.
 */
class Controller_Activity extends Controller_Base
{

    /**
     * 生成带参数的二维码.
     *
     * @return mixed
     */
    public function Action_CreateQrcode()
    {
        $sceneId = $_REQUEST['scene_id']; // 整形场景值
        $sceneStr = $_REQUEST['scene_str']; // 字符串场景值
        $actionName = $_REQUEST['action_name'];
        $expire = $_REQUEST['expire'];

        // 永久二维码有数量限制，通过配置来控制
        $sceneInfo = array();
        if ($actionName == "QR_LIMIT_SCENE" && !empty($sceneId) && in_array($sceneId, \Config\Common::$qRLimitSceneInfo['scene_id'])) {
            $sceneInfo = array('scene' => array('scene_id' => $sceneId));
        } elseif ($actionName == "QR_LIMIT_STR_SCENE" && !empty($sceneStr) && in_array($sceneStr, \Config\Common::$qRLimitSceneInfo['scene_str'])) {
            $sceneInfo = array('scene' => array('scene_str' => $sceneStr));
        } elseif ($actionName == "QR_SCENE" && !empty($sceneId)) {
            $sceneInfo = array('scene' => array('scene_id' => $sceneId));
        } elseif ($actionName == "QR_STR_SCENE" && !empty($sceneStr)) {
            $sceneInfo = array('scene' => array('scene_str' => $sceneStr));
        }
        if (empty($sceneInfo)) {
            exit(json_encode(array('errcode' => 1, 'msg' => '参数错误')));
        }

        $expire = $expire ? $expire : 86400;
        $res = \Module\Api::Instance()->getQRCode($actionName, $sceneInfo, $expire);

        // 微信接口调用失败
        if (isset($res['errcode']) && $res['errcode'] != 0) {
            $this->log($actionName . "\t" . json_encode($sceneInfo) . "\t" . json_encode($res), "createQrcodeFail");
            exit(json_encode(array('errcode' => 2, 'msg' => $res['errcode'] . ':' . $res['errmsg'])));
        }

        $qrUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($res['ticket']);

        exit(json_encode(array('errcode' => 0, 'msg' => '', 'url' => $qrUrl)));
    }

    /**
     * 长链接转短链接.
     *
     * @return mixed
     */
    public function Action_GetShortUrl()
    {
        $url = $_REQUEST['url'];
        if (empty($url) || (strpos($url, "http") !== 0 && strpos($url, "weixin://wxpay") !== 0)) {
            exit(json_encode(array('errcode' => 1, 'msg' => '参数错误')));
        }

        $res = \Module\Api::Instance()->getShortUrl($url);
        if (isset($res['errcode']) && $res['errcode'] != 0) {
            $this->log($url . "\t" . json_encode($res), "getShortUrlFail");
            exit(json_encode(array('errcode' => 2, 'msg' => $res['errcode'] . ':' . $res['errmsg'])));     // 微信接口调用失败
        }

        exit(json_encode(array('errcode' => 0, 'msg' => '', 'url' => $res['short_url'])));
    }

}
