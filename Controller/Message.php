<?php
/**
 * 消息相关.
 */

/**
 * Controller_Message.
 */
class Controller_Message extends Controller_Base
{

    private $templateApi = "https://api.weixin.qq.com/cgi-bin/message/template/send";
    private $customApi = "https://api.weixin.qq.com/cgi-bin/message/custom/send";

    /**
     * 客服消息发送，为了支持所有的消息类型，data的结构请参照微信客服消息文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547.
     *
     * @return void
     */
    public function Action_Custom()
    {
        $json = \JMGetRequest("data");
        $data = json_decode($json, true);

        /* 签名验证 */
        $postSign = isset($data['sign']) ? $data['sign'] : "";
        unset($data['sign']);
        ksort($data);
        $string = urldecode(http_build_query($data));
        $sign = strtoupper(md5(\Config\Common::$messageSignKey . $string));
        if (empty($postSign) || $postSign != $sign) {
            die(json_encode(array('errcode' => 1, 'errmsg' => "签名错误")));
        }

        if (empty($data['msgtype']) || !in_array($data['msgtype'], array('text', 'image', 'voice', 'video', 'music', 'news', 'mpnews'))) {
            die(json_encode(array('errcode' => 1, 'errmsg' => "未知的消息类型")));
        }

        $msgtype = $data['msgtype'];
        $msg = array();
        $msg['touser'] = $data['openid'];
        $msg['msgtype'] = $msgtype;
        $msg[$msgtype] = $data[$msgtype];   // 此处数据结构请参照公众号不同类型的客服消息格式

        $token = \Module\Api::Instance()->getAccessToken();
        $txt = \Util\Helper::jsonEncdoe($msg);
        $res = \Util\Requset::curlRequest($this->customApi . "?access_token=" . $token, $txt, 'post');

        $this->log($msgtype . "\t" . $data['openid'] . "\t" . $res['errcode'] . "\t" . $res['errmsg'], "messageCustom");

        $flag = (isset($res['errcode']) && $res['errcode'] == 0) ? "success" : "fail";
        die(json_encode(array('errcode' => 0, 'errmsg' => '', 'data' => array('res' => $flag))));
    }

    /**
     * 模板消息发送.
     *
     * @return void
     */
    public function Action_Template()
    {
        $json = \JMGetRequest("data");
        $data = json_decode($json, true);

        /* 签名验证 */
        $postSign = isset($data['sign']) ? $data['sign'] : "";
        unset($data['sign']);
        ksort($data);
        $string = urldecode(http_build_query($data));
        $sign = strtoupper(md5(\Config\Common::$messageSignKey . $string));
        if (empty($postSign) || $postSign != $sign) {
            die(json_encode(array('errcode' => 1, 'errmsg' => "签名错误")));
        }

        $templateConfig = \Config\Common::$templateMessageConfig;
        if (!isset($templateConfig[$data['type']])) {
            die(json_encode(array('errcode' => 1, 'errmsg' => "未知的消息类型")));
        }
        $templateInfo = $templateConfig[$data['type']];

        $templateKeys = explode(',', $templateInfo['key']);
        $color = array();
        if (!empty($data['color'])) {
            $color = explode(',', $data['color']);
        }
        $defaultColor = explode(',', $templateInfo['color']);

        $msg = array();
        $msg['touser'] = $data['openid'];
        $msg['template_id'] = $templateInfo['template_id'];
        !empty($data['url']) && $msg['url'] = $data['url'];
        if (!empty($data['miniprogram']['pagepath'])) {
            $msg['miniprogram']['pagepath'] = $data['miniprogram']['pagepath'];
            $msg['miniprogram']['appid'] = \Config\Common::$miniprogram['app_id'];
        }
        foreach ($templateKeys as $key => $templateKey) {
            $msg['data'][$templateKey] = array('value' => $data[$templateKey], 'color' => !empty($color[$key]) ? $color[$key] : $defaultColor[$key]);
        }

        $token = \Module\Api::Instance()->getAccessToken();
        $txt = \Util\Helper::jsonEncdoe($msg);
        $res = \Util\Requset::curlRequest($this->templateApi . "?access_token=" . $token, $txt, 'post');

        $this->log($data['type'] . "\t" . $data['openid'] . "\t" . $res['errcode'] . "\t" . $res['errmsg'], "messageTemplate");

        $flag = (isset($res['errcode']) && $res['errcode'] == 0) ? "success" : "fail";
        die(json_encode(array('errcode' => 0, 'errmsg' => '', 'data' => array('res' => $flag))));
    }

}
